<?php
namespace ExEss\Cms\FLW_Flows\Event\Listeners;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvents;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\Users\Service\GuidanceRecoveryService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GuidanceRecoverySubscriber implements EventSubscriberInterface
{
    private GuidanceRecoveryService $guidanceRecoveryService;

    public function __construct(GuidanceRecoveryService $guidanceRecoveryService)
    {
        $this->guidanceRecoveryService = $guidanceRecoveryService;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::INIT => [
                ['saveGuidanceDataForRecovery', 999998],
            ],
            FlowEvents::CHANGED => [
                ['saveGuidanceDataForRecovery', 999998],
            ],
            FlowEvents::NEXT_STEP => [
                ['saveGuidanceDataForRecovery', 999998],
            ],
            FlowEvents::NEXT_STEP_FORCED => [
                ['saveGuidanceDataForRecovery', 999998],
            ],
            FlowEvents::CONFIRM => [
                ['saveGuidanceDataForRecovery', 999998],
                ['deleteGuidanceRecoveryData', -999998],
            ],
        ];
    }

    /**
     * Save the full model and route from the guidance for user.
     */
    public function saveGuidanceDataForRecovery(FlowEvent $event): void
    {
        $route = $event->getRoute();
        $model = $event->getModel();

        if ($route === null || $event->getGuidanceAction() === FlowAction::READONLY) {
            // we don't save the model for readOnly guidance
            return;
        }

        $this->guidanceRecoveryService->saveGuidanceRecoveryData($route, $model, $event->getFlow()->getKey());
    }

    /**
     * Clear the last guidance for user.
     */
    public function deleteGuidanceRecoveryData(FlowEvent $event): void
    {
        $route = $event->getRoute();
        if ($route === null) {
            return;
        }

        $this->guidanceRecoveryService->resetGuidanceRecoveryData();
    }
}
