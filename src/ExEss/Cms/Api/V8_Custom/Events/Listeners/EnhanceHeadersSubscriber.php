<?php

namespace ExEss\Cms\Api\V8_Custom\Events\Listeners;

use ExEss\Cms\Api\V8_Custom\Events\ExternalApiEvent;
use ExEss\Cms\Api\V8_Custom\Events\ExternalApiEvents;
use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\Component\Session\Headers;
use ExEss\Cms\Entity\User;
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
