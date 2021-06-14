<?php declare(strict_types=1);

namespace ExEss\Cms\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ExEss\Cms\Entity\AclRole;
use ExEss\Cms\Entity\User;

class DefaultRoleListener
{
    public function prePersist(User $entity, LifecycleEventArgs $args): void
    {
        $this->process($entity, $args->getEntityManager());
    }

    private function process(User $entity, EntityManager $em): void
    {
        if (!$entity->hasRole(AclRole::DEFAULT_ROLE_CODE)) {
            $role = $em->find(AclRole::class, ['code' => AclRole::DEFAULT_ROLE_CODE]);
            if ($role instanceof AclRole) {
                $entity->addRole($role);
            }
        }
    }
}
