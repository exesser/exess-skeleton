<?php
namespace ExEss\Cms\FLW_Flows\Event\Listeners;

use Psr\Container\ContainerInterface;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvents;
use ExEss\Cms\FLW_Flows\ResponseHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResponseHandlerSubscriber implements EventSubscriberInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        // very low prio, since these always need to be the very last listener to be handled
        return [
            FlowEvents::INIT => [
                ['runResponseHandlers', -999999],
            ],
            FlowEvents::CHANGED => [
                ['runResponseHandlers', -999999],
            ],
            FlowEvents::NEXT_STEP => [
                ['runResponseHandlers', -999999],
            ],
            FlowEvents::NEXT_STEP_FORCED => [
                ['runResponseHandlers', -999999],
            ],
        ];
    }

    public function runResponseHandlers(FlowEvent $event): void
    {
        // lazy get the list of response handlers until they have a static shouldHandle method
        $handlers = $this->container->get(ResponseHandler\HandlerStack::class);

        foreach ($handlers->toArray() as $modifier) {
            if ($event->isPropagationStopped() || !$modifier->shouldModify($event)) {
                continue;
            }
            $modifier($event);
        }
    }
}
