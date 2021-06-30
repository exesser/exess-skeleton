<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Events\Listeners;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Events\AfterLoginEvent;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Events\UserEvents;
use ExEss\Bundle\CmsBundle\Entity\UserLogin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Defines a listener that subscribes to the after login event to update the last login date for a user
 */
class UpdateLastLoginSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserEvents::AFTER_LOGIN => 'updateLastLogin'
        ];
    }

    public function updateLastLogin(AfterLoginEvent $event): void
    {
        $user = $event->getUser();
        /** @var UserLogin $lastLogin */
        if (!($lastLogin = $user->getLastLogin()) instanceof UserLogin) {
            $lastLogin = new UserLogin($user);
        }
        $lastLogin->setLastLogin(new \DateTime());
        $lastLogin->setJwt($event->getJwt());

        $this->entityManager->persist($lastLogin);
        $this->entityManager->flush();
    }
}
