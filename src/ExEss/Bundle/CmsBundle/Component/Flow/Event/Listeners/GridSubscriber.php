<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Event\Listeners;

use ExEss\Bundle\CmsBundle\Entity\FlowStep;
use ExEss\Bundle\CmsBundle\Component\Flow\Event\FlowEvent;
use ExEss\Bundle\CmsBundle\Component\Flow\Event\FlowEvents;
use ExEss\Bundle\CmsBundle\Service\GridService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GridSubscriber implements EventSubscriberInterface
{
    private GridService $gridService;

    public function __construct(GridService $gridService)
    {
        $this->gridService = $gridService;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::INIT => [['getNextGrid', -10]],
            FlowEvents::CHANGED => [
                ['getCurrentGrid', 0],
                ['removeGridFromResponse', -125],    // after BreakoutSubscriber
            ],
            FlowEvents::NEXT_STEP => 'getNextGrid',
            FlowEvents::NEXT_STEP_FORCED => 'getNextGrid',
        ];
    }

    /**
     * @throws \LogicException In case NextStepSubscriber has not run yet.
     */
    public function getNextGrid(FlowEvent $event): void
    {
        if (!$event->getNextStep()) {
            throw new \LogicException('NextStepSubscriber should have run before GridSubscriber');
        }

        $this->loadGridForStep(
            $event,
            $event->getNextStep()->getFlowStep()
        );
    }

    /**
     * @throws \LogicException In case NextStepSubscriber has not run yet.
     */
    public function getCurrentGrid(FlowEvent $event): void
    {
        if (!$event->getAction()->getCurrentStep()) {
            // current step was not supplied in the request, can't do anything
            return;
        }
        if (!$event->getResponse()->getCurrentStep()) {
            throw new \LogicException('NextStepSubscriber should have run before GridSubscriber');
        }

        $this->loadGridForStep(
            $event,
            $event->getResponse()->getCurrentStep()->getFlowStep()
        );
    }

    public function removeGridFromResponse(FlowEvent $event): void
    {
        // removes grid from response after BreakoutSubscriber has run
        $event->getResponse()->setGrid(null);
    }

    private function loadGridForStep(FlowEvent $event, FlowStep $flowStep): void
    {
        $grid = $this->gridService->getGridForFlowStep(
            $flowStep,
            $event->getModel(),
            $event->getFlow(),
            $event->getRecordId()
        );

        $event->getResponse()->setGrid($grid);
    }
}
