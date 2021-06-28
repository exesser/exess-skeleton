<?php
namespace ExEss\Cms\Component\Flow\Event\Listeners;

use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Entity\FlowAction;
use ExEss\Cms\Entity\FlowField;
use ExEss\Cms\Component\Flow\Action\Command;
use ExEss\Cms\Component\Flow\ActionFactory;
use ExEss\Cms\Component\Flow\Builder\FormBuilder;
use ExEss\Cms\Component\Flow\Event\FlowEvent;
use ExEss\Cms\Component\Flow\Event\FlowEvents;
use ExEss\Cms\Component\Flow\FlowValidator;
use ExEss\Cms\Component\Flow\Response;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ValidationSubscriber implements EventSubscriberInterface
{
    private FlowValidator $validator;

    private ActionFactory $actionFactory;

    private FormBuilder $formBuilder;

    private FlashMessageContainer $flashMessageContainer;

    public function __construct(
        FlowValidator $validator,
        ActionFactory $actionFactory,
        FormBuilder $formBuilder,
        FlashMessageContainer $flashMessageContainer
    ) {
        $this->validator = $validator;
        $this->actionFactory = $actionFactory;
        $this->formBuilder = $formBuilder;
        $this->flashMessageContainer = $flashMessageContainer;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::NEXT_STEP => [
                ['validateCurrentStep', -25],
            ],
            FlowEvents::CHANGED => [
                ['validateFieldOrFlow', -150],
            ],
            FlowEvents::NEXT_STEP_FORCED => [
                ['validateFieldOrFlowOnNextStepForce', -100],
            ],
            FlowEvents::CONFIRM => [
                ['validateFlow', -25],
            ],
        ];
    }

    /**
     * @throws \LogicException If ProgressSubscriber was not run yet.
     */
    public function validateCurrentStep(FlowEvent $event): void
    {
        $response = $event->getResponse();
        if (!$response->getCurrentStep()) {
            throw new \LogicException('ProgressSubscriber should have been run before ValidationSubscriber');
        }

        $result = new Response\ValidationResult();

        $this->validator->validateFlowStep(
            $response->getCurrentStep()->getFlowStep(),
            $this->getCloneModelWithParent($event),
            $result
        );

        if (!$result->isValid()) {
            // set the found errors in the response
            $response->setErrors((object) $result->getErrors());
            // make sure we are not sending any other response blocks
            $response->setForm();
            $response->setGrid();
            $response->setGuidance();
            $response->setSteps();
            $response->setModel(new Response\Model());
            // we're done here
            $event->stopPropagation();
        }
    }

    /**
     * We have to validate the flow also on NextStepForce when we have a focus field.
     * This is true when the event is trigger from SuggestionsSubscriber.php:forceReloadStep
     */
    public function validateFieldOrFlowOnNextStepForce(FlowEvent $event): void
    {
        if ($event->getAction()->getFocus()) {
            $this->validateFieldOrFlow($event);
        }
    }

    /**
     * @throws \LogicException If ProgressSubscriber was not run yet.
     */
    public function validateFieldOrFlow(FlowEvent $event): void
    {
        $response = $event->getResponse();

        // in case the action did not contain currentStep (practically: the 'correct these errors' form upon confirm
        // of a multi step flow), validate the entire flow, in all other cases, validate just this field
        if (empty($event->getAction()->getCurrentStep())) {
            $this->validateFlow($event);
        } else {
            if (!$response->getCurrentStep()) {
                throw new \LogicException('ProgressSubscriber should have been run before ValidationSubscriber');
            }
            $errors = $this->validator->validateField(
                $response->getCurrentStep()->getFlowStep(),
                $this->getCloneModelWithParent($event),
                $event->getAction()->getFocus()
            );

            $response->setErrors($errors);
        }
    }

    public function validateFlow(FlowEvent $event): void
    {
        $result = $this->validator->validateFlow(
            $event->getFlow(),
            $this->getCloneModelWithParent($event)
        );

        if ($result->isValid()) {
            return;
        }

        if (
            \count($event->getFlow()->getSteps()) < 2
            || \count($result->getErrors()) > \count($result->getFields()) // we have errors in repeatable blocks
        ) {
            // in this case on confirm we don't return a model to fix the errors,
            // we just display them on the original guidance + some flash messages
            $event->getResponse()->setErrors((object) $result->getErrors());

            // display flash messages with the errors for all the hidden fields with `1==1`
            foreach ($result->getFields() as $field) {
                if ($field->getHideExpression() === "1==1") {
                    $fieldId = $field->getFieldId();
                    $this->flashMessageContainer->addFlashMessage(
                        new FlashMessage(
                            $fieldId . ": " . \implode(' ', $result->getErrors()[$fieldId]),
                            FlashMessage::TYPE_ERROR,
                            $fieldId
                        )
                    );
                }
            }

            // display flash messages for fields in repeatable blocks
            $fieldNames = \array_map(function (FlowField $field) {
                return $field->getFieldId();
            }, $result->getFields());

            foreach ($result->getErrors() as $namespace => $blocks) {
                if (\in_array($namespace, $fieldNames, true)) {
                    //is not a repeatable block, is a field
                    continue;
                }

                foreach ($blocks as $blockKey => $fields) {
                    foreach ($fields as $fieldKey => $errors) {
                        $this->flashMessageContainer->addFlashMessage(
                            new FlashMessage(
                                \sprintf('%s: %s', $fieldKey, \implode(' ', $errors)),
                                FlashMessage::TYPE_ERROR,
                                $fieldKey
                            )
                        );
                    }
                }
            }
        } else {
            $event->setCommand($this->getValidationFailedCommand($event, $result));
        }

        $event->stopPropagation();
    }

    /**
     * @throws InvalidArgumentException In case of invalid arguments.
     */
    private function getValidationFailedCommand(
        FlowEvent $event,
        Response\ValidationResult $validationResult
    ): Command {
        if ($validationResult->isValid()) {
            throw new InvalidArgumentException('This is only relevant for CONFIRM events that had validation errors');
        }

        $command = $this->actionFactory->getCommand(
            FlowAction::KEY_MODAL_VALIDATION_ERROR_FLOW,
            $event->getAction()->getRecordIds()
        );

        if ($command === null) {
            throw new InvalidArgumentException('Could not retrieve the command for the errors modal');
        }

        $this->formBuilder->setFilledErrorForm(
            $command->getArguments(),
            $event->getFlow(),
            $event->getModel(),
            $validationResult,
            $event->getBaseEntity()
        );

        return $command;
    }

    private function getCloneModelWithParent(FlowEvent $event): Response\Model
    {
        $model = $event->getResponse()->getModel();
        $clone = clone $model;
        if ($parent = $event->getResponse()->getParentModel()) {
            $clone->setFieldValue(Dwp::PARENT_MODEL, $parent);
        }

        return $clone;
    }
}
