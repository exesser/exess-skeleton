<?php declare(strict_types=1);

namespace ExEss\Cms\EventListener;

use ExEss\Cms\Entity\User;

class PrimaryGroupListener
{
    public function preFlush(User $entity): void
    {
        $this->process($entity);
    }

    private function process(User $entity): void
    {
        if ($entity->getPrimaryGroup() !== null || !($group = $entity->getUserGroups()->current())) {
            return;
        }

        $group->setPrimaryGroup(true);
    }
}
