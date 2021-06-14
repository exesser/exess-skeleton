<?php

namespace ExEss\Cms\FLW_Flows\Event\Listeners;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvents;
use ExEss\Cms\Dashboard\GridRepository;
use ExEss\Cms\FLW_Flows\Action\Command;
use ExEss\Cms\FLW_Flows\ActionFactory;
use ExEss\Cms\FLW_Flows\AfterSave\AfterSaveData;
use ExEss\Cms\FLW_Flows\AfterSave\Handler\AfterSaveHandlerQueue;
use ExEss\Cms\FLW_Flows\Builder\FormBuilder;
use ExEss\Cms\FLW_Flows\Event\Exception\CommandException;
use ExEss\Cms\FLW_Flows\Handler\FlowData;
use ExEss\Cms\FLW_Flows\MultiLineFlowSaver;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\SaveFlow;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SaveSubscriber implements EventSubscriberInterface
{
    private GridRepository $gridRepository;

    private SaveFlow $saveFlow;

    private MultiLineFlowSaver $multiLineFlowSaver;

    private ActionFactory $actionFactory;

    private AfterSaveHandlerQueue $afterSaveHandlerQueue;

    private FormBuilder $formBuilder;

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        GridRepository $gridRepository,
        SaveFlow $saveFlow,
        MultiLineFlowSaver $multiLineFlowSaver,
        ActionFactory $actionFactory,
        AfterSaveHandlerQueue $afterSaveHandlerQueue,
        FormBuilder $formBuilder
    ) {
        $this->gridRepository = $gridRepository;
        $this->saveFlow = $saveFlow;
        $this->actionFactory = $actionFactory;
        $this->multiLineFlowSaver = $multiLineFlowSaver;
        $this->afterSaveHandlerQueue = $afterSaveHandlerQueue;
        $this->formBuilder = $formBuilder;
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::CONFIRM => [
                ['handleFlowSave', -100],
            ],
        ];
    }

    public function handleFlowSave(FlowEvent $event): void
    {
        $recordId = null;

        $model = $event->getResponse()->getModel();
        $parentModel = $event->getResponse()->getParentModel();
        $flow = $event->getFlow();

        try {
            if ($this->gridRepository->hasFlowRepeatableRows($flow)) {
                $allFlowData = $this->multiLineFlowSaver->save($flow, $model);

                $recordId = $allFlowData->getFlowData()->getRecordId() ?? false;
                $this->afterSaveHandlerQueue->apply($allFlowData);
                $relatedBeansIds = $this->findAllRelatedBeansInFlow($allFlowData->getFlowData());
            } else {
                $modelRecordIds = $model->recordIds ? $model->recordIds->toArray() : [];

                if (!empty($modelRecordIds) && (empty($model->id) || \count($modelRecordIds) > 1)) {
                    // We run the save for each recordId we got, but first update the model with the new
                    // default values for all hidden fields, if they depend on the id of the base entity.
                    $steps = $flow->getSteps();
                    foreach ($modelRecordIds as $recordId) {
                        $model->id = $recordId;
                        if ($model->offsetExists('recordId')) {
                            $model->offsetSet('recordId', $recordId);
                        }
                        if (!$flow->isExternal() && !empty($flow->getBaseObject())) {
                            $baseEntity = $this->em->getRepository($flow->getBaseObject())->find($recordId);
                            foreach ($steps as $step) {
                                $form = $this->formBuilder->getFilledFlowStepForm(
                                    $flow,
                                    $step,
                                    $model,
                                    $baseEntity,
                                    FlowAction::EVENT_INIT
                                );
                                foreach ($form->getFields() as $field) {
                                    if (
                                        $field->id !== 'id'
                                        && $field->type === 'hidden'
                                        && !empty($field->default)
                                        && $model->offsetExists($field->id)
                                    ) {
                                        $model->offsetSet($field->id, $field->default);
                                    }
                                }
                            }
                        }

                        $this->runSaveFlow($flow, $model, $parentModel);
                    }
                    // We know we don't set the related fat entities here because it's not useful to do this here
                    $relatedBeansIds = [];
                } else {
                    $flowData = $this->runSaveFlow($flow, $model);
                    $recordId = $flowData->getRecordId() ?? false;
                    $relatedBeansIds = $this->findAllRelatedBeansInFlow($flowData);
                }
            }
        } catch (CommandException $e) {
            $event->getResponse()->setModel($model);
            $event->stopPropagation();
            $relatedBeansIds = [];
        }

        if (\count($event->getAction()->getRecordIds()) <= 1) {
            $recordIds = [$recordId];
        } else {
            $recordIds = $event->getAction()->getRecordIds();
        }

        $event->setCommand($this->getReturnCommand(
            $flow,
            $recordIds,
            $model,
            $relatedBeansIds
        ));
    }

    private function findAllRelatedBeansInFlow(FlowData $flowData): array
    {
        $entities = [];
        foreach ($flowData->getEntities() as $name => $objects) {
            foreach ($objects as $object) {
                $entities[$name][] = $object->getId();
            }
        }

        return $entities;
    }

    private function runSaveFlow(Flow $flow, Model $model, ?Model $parentModel = null): FlowData
    {
        $flowData = $this->saveFlow->save($flow, $model, $parentModel);

        // Run only when everything has been saved, parent flow and all repeatable block flows
        if (null === $parentModel) {
            $this->afterSaveHandlerQueue->apply(new AfterSaveData($flowData));
        }

        return $flowData;
    }

    private function getReturnCommand(
        Flow $flow,
        array $recordIds,
        Model $model,
        array $relatedBeansIds = []
    ): ?Command {
        $actionKey = $model->{Dwp::FLAG_CONFIRM_ACTION_KEY} ?? $flow->getAction();
        $command = $this->actionFactory->getCommand($actionKey, $recordIds, null, ['model' => $model]);
        if ($command) {
            $command->setRelatedBeans($relatedBeansIds);
        }

        return $command;
    }
}
