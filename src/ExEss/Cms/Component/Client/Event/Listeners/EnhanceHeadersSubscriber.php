<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Client\Event\Listeners;

use ExEss\Cms\Component\Client\Request\GuzzleRequest;
use ExEss\Cms\Component\Session\Headers;
use ExEss\Cms\Component\Client\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribes to the external api events
 * to enhance the headers sent to that service.
 */
class EnhanceHeadersSubscriber implements EventSubscriberInterface
{
    private Headers $headers;

    public function __construct(Headers $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'enhanceHeaders',
        ];
    }

    public function enhanceHeaders(RequestEvent $event): void
    {
        if (!$event->getRequest() instanceof GuzzleRequest) {
            return;
        }

        $event->setExtraHeaders($this->headers->fetchXLogHeadersForNextCall());
    }
}
