<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Client\Event\Listeners;

use ExEss\Cms\Component\Client\Event\ExceptionEvent;
use ExEss\Cms\Component\Client\Event\RequestEvent;
use ExEss\Cms\Component\Client\Event\ResponseEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribes to the external api events
 * to log incoming and outgoing calls.
 */
class LogSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $clientRequestLogger;

    private LoggerInterface $clientResponseLogger;

    private LoggerInterface $clientExceptionLogger;

    public function __construct(
        LoggerInterface $clientRequestLogger,
        LoggerInterface $clientResponseLogger,
        LoggerInterface $clientExceptionLogger
    ) {
        $this->clientRequestLogger = $clientRequestLogger;
        $this->clientResponseLogger = $clientResponseLogger;
        $this->clientExceptionLogger = $clientExceptionLogger;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [['logRequest', -100]],
            ResponseEvent::class => 'logResponse',
            ExceptionEvent::class => 'logException',
        ];
    }

    public function logRequest(RequestEvent $event): void
    {
        $this->clientRequestLogger->info(\sprintf(
            "Outgoing call to %s: %s",
            $event->getRequest()->getPath(),
            \json_encode([
                "client" => $event->getClient()->getClientConfig()->getUrl(),
                "request" => $event->getRequest(),
                "options" => $event->getOptions(),
            ])
        ));
    }

    public function logResponse(ResponseEvent $event): void
    {
        $this->clientResponseLogger->info(\sprintf(
            "Incoming response for %s: %s",
            $event->getRequest()->getPath(),
            \json_encode([
                "client" => $event->getClient()->getClientConfig()->getUrl(),
                "response" => $event->getResponse(),
            ]),
        ));
    }

    public function logException(ExceptionEvent $event): void
    {
        $this->clientExceptionLogger->critical(\sprintf(
            "Exception consuming %s: %s",
            $event->getRequest()->getPath(),
            \json_encode([
                "client" => $event->getClient()->getClientConfig()->getUrl(),
                "exception" => $event->getException()->getMessage(),
            ])
        ));
    }
}
