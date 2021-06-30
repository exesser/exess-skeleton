<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Security\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSuccessSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => [['success', 0]],
        ];
    }

    public function success(LogoutEvent $event): void
    {
        $event->setResponse(new JsonResponse('{"success": true}', 200, [], true));
    }
}
