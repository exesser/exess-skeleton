<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Http\EventSubscriber;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Bundle\CmsBundle\Http\AbstractJsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FlashMessageSubscriber implements EventSubscriberInterface
{
    private FlashMessageContainer $flashMessages;

    public function __construct(
        FlashMessageContainer $flashMessages
    ) {
        $this->flashMessages = $flashMessages;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // low priority to come after regular response listeners, but higher than StreamedResponseListener
            KernelEvents::RESPONSE => ['onKernelResponse', -1000],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$this->flashMessages->count()) {
            return;
        }

        /** @var AbstractJsonResponse $response */
        $response = $event->getResponse();

        $response->addFlashMessages($this->flashMessages->getFlashMessages());

        $this->flashMessages->reset();
    }
}
