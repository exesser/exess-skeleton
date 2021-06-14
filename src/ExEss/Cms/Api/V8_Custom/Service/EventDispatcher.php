<?php

namespace ExEss\Cms\Api\V8_Custom\Service;

use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * extend the symfony event dispatcher and add lazy initialization of event subscribers
 */
class EventDispatcher extends \Symfony\Component\EventDispatcher\EventDispatcher
{
    public const EVENT_DISPATCHER_INVOICE_ATTACHMENTS = 'event_dispatcher.invoice.attachments';

    protected ContainerInterface $container;

    /**
     * @var string[]
     */
    protected array $lazySubscribers = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function initializeLazySubscribers(?string $eventName = null): void
    {
        if (!\count($this->lazySubscribers)) {
            return;
        }

        $lazySubscribers = $this->lazySubscribers;
        $this->lazySubscribers = [];
        while (\count($lazySubscribers)) {
            $service = \array_shift($lazySubscribers);
            // if an event name was given, only add the subscribers that have events listening to this event
            if (!$eventName
                || !\class_exists($service)
                || !$service instanceof EventSubscriberInterface
                || \array_key_exists($eventName, $service::getSubscribedEvents())
            ) {
                $this->addSubscriber($this->container->get($service));
            } else {
                $this->lazySubscribers[] = $service;
            }
        }
    }

    public function addLazySubscriber(string $serviceName): void
    {
        $this->lazySubscribers[] = $serviceName;
    }

    /**
     * @inheritdoc
     */
    public function dispatch(object $event, ?string $eventName = null): object
    {
        $eventName = $eventName ?? \get_class($event);
        $this->initializeLazySubscribers($eventName);
        return parent::dispatch($event, $eventName);
    }

    /**
     * @inheritdoc
     */
    public function getListeners($eventName = null)
    {
        $this->initializeLazySubscribers($eventName);
        return parent::getListeners($eventName);
    }

    /**
     * @inheritdoc
     */
    public function getListenerPriority($eventName, $listener)
    {
        $this->initializeLazySubscribers($eventName);
        return parent::getListenerPriority($eventName, $listener);
    }

    /**
     * @inheritdoc
     */
    public function hasListeners($eventName = null)
    {
        $this->initializeLazySubscribers($eventName);
        return parent::hasListeners($eventName);
    }

    /**
     * @inheritdoc
     */
    public function removeListener($eventName, $listener)
    {
        $this->initializeLazySubscribers($eventName);
        parent::removeListener($eventName, $listener);
    }

    /**
     * @inheritdoc
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->initializeLazySubscribers();
        parent::removeSubscriber($subscriber);
    }
}
