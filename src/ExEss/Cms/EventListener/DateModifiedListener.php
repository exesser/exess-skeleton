<?php declare(strict_types=1);

namespace ExEss\Cms\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;

class DateModifiedListener
{
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($args->getObjectManager()->getClassMetadata(\get_class($entity))->hasField('dateModified')) {
            $entity->setDateModified(new \DateTimeImmutable());
        }
    }
}
