<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Session\User;

interface UserInterface
{
    public function getId(): ?string;
    public function getUsername(): ?string;
}
