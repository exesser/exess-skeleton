<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Command\Traits;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\User\DefaultUser;
use ExEss\Bundle\CmsBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait LoginTrait
{
    public function login(TokenStorageInterface $tokenStorage, DefaultUser $admin): void
    {
        $this->loginAs($tokenStorage, $admin->getSystemUser());
    }

    public function loginAs(TokenStorageInterface $tokenStorage, ?User $user): TokenInterface
    {
        $token = null;
        if ($user !== null) {
            $tokenStorage->setToken(
                $token = new UsernamePasswordToken($user, null, 'api', $user->getRoles())
            );
        }

        return $token;
    }
}
