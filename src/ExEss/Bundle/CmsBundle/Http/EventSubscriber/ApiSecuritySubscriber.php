<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Http\EventSubscriber;

use ExEss\Bundle\CmsBundle\Exception\NotAuthorizedException;
use ExEss\Bundle\CmsBundle\Http\Factory\PsrFactory;
use ExEss\Bundle\CmsBundle\Security\Route\DecisionManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiSecuritySubscriber implements EventSubscriberInterface
{
    private DecisionManager $decisionManager;

    private PsrFactory $psrFactory;

    public function __construct(PsrFactory $psrFactory, DecisionManager $decisionManager)
    {
        $this->decisionManager = $decisionManager;
        $this->psrFactory = $psrFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // after firewall, we need the route and the authentication!
            KernelEvents::REQUEST => ['checkApiAccess', -50],
        ];
    }

    public function checkApiAccess(RequestEvent $event): void
    {
        if (!$this->decisionManager->hasAccess(
            $this->psrFactory->createRequest($event->getRequest())
        )) {
            throw new NotAuthorizedException();
        }
    }
}
