<?php
namespace ExEss\Cms\Api\V8_Custom\Service;

use Psr\Container\ContainerInterface;

class EventDispatcherFactory
{
    public static function getEventDispatcher(
        ContainerInterface $container,
        iterable $subscribers = []
    ): EventDispatcher {
        $eventDispatcher = new EventDispatcher($container);
        foreach ($subscribers as $subscriber => $factory) {
            $eventDispatcher->addLazySubscriber($subscriber);
        }

        return $eventDispatcher;
    }
}
