<?php
namespace ExEss\Cms\FLW_Flows\Event\Listeners;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvents;
use ExEss\Cms\FLW_Flows\Guidance;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GuidanceSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::INIT => 'injectGuidance',
            FlowEvents::NEXT_STEP => 'injectGuidance',
            FlowEvents::NEXT_STEP_FORCED => 'injectGuidance',
        ];
    }

    public function injectGuidance(FlowEvent $event): void
    {
        $flow = $event->getFlow();

        $guidance = new Guidance();
        $guidance->title = $flow->getLabel();
        $guidance->loadingMessage = $flow->getLoadingMessage();

        $event->getResponse()->setGuidance($guidance);
    }
}
