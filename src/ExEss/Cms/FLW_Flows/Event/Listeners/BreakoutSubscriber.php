<?php
namespace ExEss\Cms\FLW_Flows\Event\Listeners;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvents;
use ExEss\Cms\FLW_Flows\Event\FlowEventDispatcher;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\Service\RepeatableRowService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BreakoutSubscriber implements EventSubscriberInterface
{
    private RepeatableRowService $repeatableRowService;

    private FlowEventDispatcher $flowEventDispatcher;

    public function __construct(
        RepeatableRowService $repeatableRowService,
        FlowEventDispatcher $flowEventDispatcher
    ) {
        $this->repeatableRowService = $repeatableRowService;
        $this->flowEventDispatcher = $flowEventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::CHANGED => [
                ['rerouteIfNeeded', -50],
                ['breakIfNeeded', -200],
            ],
            FlowEvents::CONFIRM_CREATE_LIST_ROW => [
                ['breakIfNeeded', -100],
            ],
            FlowEvents::NEXT_STEP => [
                ['breakIfNeeded', -100],
            ],
            FlowEvents::NEXT_STEP_FORCED => [
                ['breakIfNeeded', -100],
            ],
        ];
    }

    public function breakIfNeeded(FlowEvent $event): void
    {
        if ($event->getAction()->getEvent() === FlowAction::EVENT_CONFIRM_CREATE_LIST_ROW) {
            // for this event we're done here
            $event->stopPropagation();
        }
    }

    /**
     * @throws \LogicException In case GridSubscriber has not run yet.
     */
    public function rerouteIfNeeded(FlowEvent $event): void
    {
        $action = $event->getAction();
        $response = $event->getResponse();

        if (!$action->getCurrentStep()) {
            // current step was not supplied in the request, can't do anything
            return;
        }
        if (!$response->getGrid()) {
            throw new \LogicException('GridSubscriber should have run before BreakoutSubscriber');
        }

        // check if we need to do something
        if (!$this->shouldReroute($event)) {
            return;
        }

        // redispatch to a NEXT_STEP_FORCED event
        $newAction = new FlowAction([
            'event' => FlowAction::EVENT_NEXT_STEP_FORCED,
            'currentStep' => $action->getCurrentStep(),
            'nextStep' => $action->getCurrentStep(),
            'recordIds' => $action->getRecordIds(),
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

    private function shouldReroute(FlowEvent $event): bool
    {
        $action = $event->getAction();
        $model = $event->getModel();
        $response = $event->getResponse();

        $repeatField = null;
        $hasRepeatableBlocks = $this->repeatableRowService->hasRepeatableRowIn($response->getGrid());
        if ($hasRepeatableBlocks) {
            $repeatableRow = $this->repeatableRowService->getRepeatableRowsIn($response->getGrid())[0];
            $repeatField = $repeatableRow->getOptions()->getRepeatsBy();
        }

        return $hasRepeatableBlocks
            && !empty($action->getFocus())
            && \in_array(
                $action->getFocus(),
                $model->findFieldKeys([$repeatField]),
                true
            )
        ;
    }
}
