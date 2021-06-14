<?php

namespace ExEss\Cms\Api\V8_Custom\Events;

use ExEss\Cms\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class AfterLoginEvent extends Event
{
    private User $user;

    private string $jwt;

    public function __construct(User $user, string $jwt)
    {
        $this->user = $user;
        $this->jwt = $jwt;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getJwt(): string
    {
        return $this->jwt;
    }
}
