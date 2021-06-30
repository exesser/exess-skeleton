<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Events\Listeners;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Events\ExternalApiEvent;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Events\ExternalApiEvents;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\Security;
use ExEss\Bundle\CmsBundle\Component\Session\Headers;
use ExEss\Bundle\CmsBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribes to the external api events
 * to enhance the headers sent to that service.
 */
class EnhanceHeadersSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ExternalApiEvents::REQUEST => 'enhanceHeaders',
            ExternalApiEvents::PREPARE_REQUEST => 'enhanceHeaders',
        ];
    }

    public function enhanceHeaders(ExternalApiEvent $event): void
    {
        $neededHeaders = Headers::create();
        $user = $this->security->getCurrentUser();
        if ($user instanceof User) {
            $neededHeaders->setUser($user);
        }

        $event->setExtraHeaders($neededHeaders->all());
    }
}
