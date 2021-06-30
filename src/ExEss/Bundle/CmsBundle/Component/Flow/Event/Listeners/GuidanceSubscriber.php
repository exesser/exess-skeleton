<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Event\Listeners;

use ExEss\Bundle\CmsBundle\Component\Flow\Event\FlowEvent;
use ExEss\Bundle\CmsBundle\Component\Flow\Event\FlowEvents;
use ExEss\Bundle\CmsBundle\Component\Flow\Guidance;
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
