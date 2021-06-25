<?php

namespace ExEss\Cms\FLW_Flows\Event\Listeners;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Helper\DataCleaner;
use ExEss\Cms\Service\GridService;
use Psr\Container\ContainerInterface;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvents;
use ExEss\Cms\Exception\HandlerNotFoundException;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Model;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PreValidationSubscriber implements EventSubscriberInterface
{
    protected ContainerInterface $container;

    /**
     * @var iterable
     */
    protected iterable $preValidationHandlers;

    protected GridService $gridService;

    private EntityManager $em;

    public function __construct(
        ContainerInterface $container,
        iterable $preValidationHandlers,
        EntityManager $em,
        GridService $gridService
    ) {
        $this->container = $container;
        $this->preValidationHandlers = $preValidationHandlers;
        $this->gridService = $gridService;
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::INIT => [
                ['runSuggestionsOnModel', -40],
            ],
            FlowEvents::NEXT_STEP => [
                ['runSuggestionsOnModel', -24],
                ['applyParentFieldsOnChildren', -60],
            ],
            FlowEvents::NEXT_STEP_FORCED => [
                ['runSuggestionsOnModel', -50],
                ['applyParentFieldsOnChildren', -60],
            ],
            FlowEvents::CHANGED => [
                ['runSuggestionsOnModel', -149],
                ['applyParentFieldsOnChildren', -60],
            ],
            FlowEvents::CONFIRM => [
                ['runSuggestionsOnParentAndChildModels', -24],
            ],
        ];
    }

    /**
     * @throws HandlerNotFoundException In case the given handler could not be found in the container.
     */
    public function runSuggestionsOnModel(FlowEvent $event): void
    {
        // the model is sent to us individually
        // in case of a parent flow event: parent model with it's children
        // in case of a child flow event: child model with nested in it a dwp|parentModel key with the parent data
        $this->runFor($event->getResponse(), $event->getAction(), $event->getFlow());
    }

    /**
     * Merge field from parent to child if they are on same namespace.
     */
    public function applyParentFieldsOnChildren(FlowEvent $event): void
    {
        $this->mergeParentAndChildrenFieldsFromSameNamespace($event, $event->getResponse()->getModel());
    }

    public function runSuggestionsOnParentAndChildModels(FlowEvent $event): void
    {
        // nothing special to be done for flows without repeating sub flows
        $flow = $event->getFlow();
        if (!$this->gridService->hasFlowRepeatableRows($flow)) {
            $this->runFor($event->getResponse(), $event->getAction(), $flow);

            return;
        }

        // the model is sent in entirety, meaning a parent model with nested child models so we have to run the
        // suggestions for parent and each child model, and mimic the other event's behavior by enriching the child
        // model with the parent model's data as DWP is not adding it to the children models
        $parentModel = $event->getResponse()->getModel();

        // first, run the suggestion for the parent
        $this->runFor($event->getResponse(), $event->getAction(), $flow);

        // now divide parent and children, and run the suggestions for each child model
        $allChildModels = $this->mergeParentAndChildrenFieldsFromSameNamespace($event, $parentModel);
        $allRepeatableFlows = $this->getRepeatableFlows($flow);

        foreach ($allChildModels as $namespace => $childModels) {
            /** @var Model $childModel */
            foreach ($childModels as $childModel) {
                $response = new Response();
                $response->setModel($childModel);
                $response->setParentModel($parentModel);

                // run the suggestions on these model
                foreach ($allRepeatableFlows[$namespace] ?? [] as $repeatableFlowKey) {
                    $this->runFor(
                        $response,
                        $event->getAction(),
                        $this->em->getRepository(Flow::class)->get($repeatableFlowKey)
                    );
                }
            }
        }
    }

    private function mergeParentAndChildrenFieldsFromSameNamespace(FlowEvent $event, Model $model): array
    {
        $repeatedNameSpaces = [];
        foreach ($this->gridService->getRepeatableRows($event->getFlow()) as $repeatableRow) {
            $childModels = [];
            $modelKey = $repeatableRow->getOptions()->getModelKey();
            if (\array_key_exists($modelKey, $repeatedNameSpaces)) {
                // skip, already done this one
                continue;
            }

            $repeatValues = $this->gridService->getRepeatValuesFromModel($repeatableRow, $model);

            // make sure the key holding the child models exists
            if (!$model->$modelKey instanceof Model) {
                $model->$modelKey = new Model();
            }

            // don't keep object references to the possible original (sub)models in the parent
            $childKeysInParent = DataCleaner::jsonDecode(\json_encode($model->getNamespace($modelKey)));
            unset($childKeysInParent[$modelKey]);

            // merge parent data into the child's model
            foreach ($repeatValues as $repeatKey) {
                $childModel = $model->$modelKey->$repeatKey ?? new Model();
                $childModel->merge(new Model($childKeysInParent));
                $childModels[$repeatKey] = $childModel;
            }

            $repeatedNameSpaces[$modelKey] = $childModels;
            $model->$modelKey = $childModels;
        }

        return $repeatedNameSpaces;
    }

    private function getRepeatableFlows(Flow $flow): array
    {
        $repeatedNameSpaces = [];
        foreach ($this->gridService->getRepeatableRows($flow) as $repeatableRow) {
            $modelKey = $repeatableRow->getOptions()->getModelKey();
            $repeatedNameSpaces[$modelKey][] = $repeatableRow->getOptions()->getFlowId();
        }

        return $repeatedNameSpaces;
    }

    private function runFor(Response $response, FlowAction $action, Flow $flow): Model
    {
        foreach ($this->preValidationHandlers as $handler) {
            if (!$handler::shouldHandle($response, $action, $flow)) {
                continue;
            }
            $handler->handleModel($response, $action, $flow);
        }
        return $response->getModel();
    }
}
