<?php
namespace ExEss\Cms\Api\V8_Custom\Service;

use ExEss\Cms\Entity\SecurityGroup;
use ExEss\Cms\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Security
{
    private TokenStorageInterface $tokenStorage;

    private string $fallbackLocale;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        string $fallbackLocale
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->fallbackLocale = $fallbackLocale;
    }

    public function getCurrentUser(): ?User
    {
        if ($this->tokenStorage->getToken() !== null
            && $this->tokenStorage->getToken()->getUser() instanceof UserInterface
        ) {
            return $this->tokenStorage->getToken()->getUser();
        }

        return null;
    }

    public function getCurrentUserId(): ?string
    {
        return $this->getCurrentUser()->getId() ?? null;
    }

    public function getPrimaryGroup(): ?SecurityGroup
    {
        if (null === $this->getCurrentUser()->getId()) {
            return null;
        }

        return $this->getCurrentUser()->getPrimaryGroup();
    }

    public function getPrimaryGroupId(): ?string
    {
        return ($group = $this->getPrimaryGroup()) ? $group->getId() : null;
    }

    public function getPreferredLocale(): string
    {
        if (null !== $this->getCurrentUser() && !empty($this->getCurrentUser()->getPreferredLocale())) {
            return $this->getCurrentUser()->getPreferredLocale();
        }

        return $this->fallbackLocale;
    }
}
