<?php
namespace ExEss\Cms\FLW_Flows\Event\Listeners;

use Psr\Container\ContainerInterface;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvents;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Exception\HandlerNotFoundException;
use ExEss\Cms\FLW_Flows\Event\FlowEventDispatcher;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response\Form;
use ExEss\Cms\FLW_Flows\Suggestions\SuggestionHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SuggestionsSubscriber implements EventSubscriberInterface
{
    private ContainerInterface $container;

    /**
     * @var iterable|SuggestionHandler[]
     */
    private iterable $suggestionHandlers;

    private FlowEventDispatcher $flowEventDispatcher;

    public function __construct(
        FlowEventDispatcher $flowEventDispatcher,
        ContainerInterface $container,
        iterable $suggestionHandlers
    ) {
        $this->container = $container;
        $this->suggestionHandlers = $suggestionHandlers;
        $this->flowEventDispatcher = $flowEventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::INIT => [
                ['injectSuggestions', -50],
            ],
            FlowEvents::NEXT_STEP => [
                ['injectSuggestions', -50],
            ],
            FlowEvents::NEXT_STEP_FORCED => [
                ['injectSuggestions', -50],
            ],
            FlowEvents::CHANGED => [
                ['injectSuggestions', -50],
            ],
            FlowEvents::CONFIRM_CREATE_LIST_ROW => [
                ['injectSuggestions', -50],
            ],
        ];
    }

    /**
     * @throws HandlerNotFoundException When the handler is not found in the container.
     */
    public function injectSuggestions(FlowEvent $event): void
    {
        $action = $event->getAction();
        $response = $event->getResponse();
        $form = $response->getForm();

        if ($form instanceof Form) {
            $fieldsBefore = $form->getFieldList();
        }

        /** @var \ExEss\Cms\FLW_Flows\Suggestions\SuggestionHandler $handler */
        foreach ($this->suggestionHandlers as $handler) {
            if (!$handler::shouldHandle($response, $action, $event->getFlow())) {
                continue;
            }
            $handler->handleModel($response, $action, $event->getFlow());

            if ($response->isForceReload()) {
                $this->forceReloadStep($event);
                return;
            }
        }

        // if one or more suggestions have changed the form during a CHANGE event, we need to redispatch the event
        if (!isset($fieldsBefore) || $fieldsBefore === $form->getFieldList()) {
            return;
        }

        $diffFields = \array_values(\array_diff($form->getFieldList(), $fieldsBefore));
        $model = $response->getModel();

        if (
            $model->offsetExists(Dwp::DYNAMIC_LOADED_FIELDS) &&
            $model->offsetGet(Dwp::DYNAMIC_LOADED_FIELDS)->toArray() === $diffFields
        ) {
            return;
        }

        $model->offsetSet(Dwp::DYNAMIC_LOADED_FIELDS, $diffFields);

        if ($action->getEvent() !== FlowAction::EVENT_CHANGED) {
            return;
        }

        $this->forceReloadStep($event);
    }

    private function forceReloadStep(FlowEvent $event): void
    {
        $action = $event->getAction();
        $response = $event->getResponse();
        $response->setForceReload(false);

        $newEvent = FlowAction::EVENT_NEXT_STEP_FORCED;
        if ($action->getEvent() === FlowAction::EVENT_INIT) {
            $newEvent = FlowAction::EVENT_INIT;
        }

        $newAction = new FlowAction([
            'event' => $newEvent,
            'currentStep' => $action->getCurrentStep(),
            'nextStep' => $action->getCurrentStep(),
            'recordIds' => $action->getRecordIds(),
            'focus' => $action->getFocus(),
            'previousValue' => $action->getPreviousValue(),
            'changedFields' => $action->getChangedFields(),
        ]);

        $newResponse = $this->flowEventDispatcher->dispatch(
            $event->getFlowKey(),
            $newAction,
            $response->getModel(),
            $response->getParentModel(),
            ['returnDelta' => false]
        );

        $response->setFromOther($newResponse);

        // wrap up the current event
        $event->stopPropagation();
    }
}
