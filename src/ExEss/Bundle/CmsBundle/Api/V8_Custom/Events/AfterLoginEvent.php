<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Events;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class AfterLoginEvent extends Event
{
    private UserInterface $user;

    private string $jwt;

    public function __construct(UserInterface $user, string $jwt)
    {
        $this->user = $user;
        $this->jwt = $jwt;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getJwt(): string
    {
        return $this->jwt;
    }
}
