<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Events\Listeners;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Events\ExternalApiEvent;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Events\ExternalApiEvents;
use ExEss\Bundle\CmsBundle\Logger\Logger;
use ExEss\Bundle\CmsBundle\Logger\Message\ChannelMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Defines a listener that subscribes to the external api events
 * to log incoming and outgoing calls.
 */
class ExternalApiLogSubscriber implements EventSubscriberInterface
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ExternalApiEvents::REQUEST => 'logRequest',
            ExternalApiEvents::SEND_PREPARED_REQUEST => 'logRequest',
            ExternalApiEvents::RESPONSE => 'logResponse',
            ExternalApiEvents::EXCEPTION => 'logException',
        ];
    }

    public function logRequest(ExternalApiEvent $event): void
    {
        $this->log($event, 'Outgoing call to', 'info');
    }

    public function logResponse(ExternalApiEvent $event): void
    {
        $this->log($event, 'Incoming response for', 'info');
    }

    public function logException(ExternalApiEvent $event): void
    {
        $this->log($event, 'exception consuming', 'critical');
    }

    private function log(
        ExternalApiEvent $event,
        string $messagePrefix,
        string $logLevel
    ): void {
        $message = ChannelMessage::byChannel(
            $messagePrefix . ' ' . $event->getUri() . ': ' . \json_encode($event->getOptions()),
            $event->getChannel()
        );
        $this->logger->{$logLevel}($message);
    }
}
