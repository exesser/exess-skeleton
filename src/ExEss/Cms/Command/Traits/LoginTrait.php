<?php declare(strict_types=1);

namespace ExEss\Cms\Command\Traits;

use ExEss\Cms\Api\V8_Custom\Service\User\DefaultUser;
use ExEss\Cms\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait LoginTrait
{
    public function login(TokenStorageInterface $tokenStorage, DefaultUser $admin): void
    {
        $this->loginAs($tokenStorage, $admin->getSystemUser());
    }

    public function loginAs(TokenStorageInterface $tokenStorage, ?User $user): void
    {
        if ($user !== null) {
            $tokenStorage->setToken(
                new UsernamePasswordToken($user, null, 'api', $user->getRoles())
            );
        }
    }
}
