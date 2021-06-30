<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\EventListener;

use Doctrine\ORM\Event\PreFlushEventArgs;

class DateEnteredListener
{
    public function preFlush(PreFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();

        foreach ($em->getUnitOfWork()->getScheduledEntityInsertions() as $entity) {
            $metadata = $em->getClassMetadata(\get_class($entity));

            if (!$metadata->hasField('dateEntered')) {
                return;
            }

            $reflectionClass = $metadata->getReflectionClass();
            $dateEntered = $reflectionClass->getProperty('dateEntered');
            $dateEntered->setAccessible(true);
            try {
                $dateEntered->getValue($entity);
            } catch (\Error $e) {
                if (\stristr($e->getMessage(), 'must not be accessed before initialization')) {
                    $entity->setDateEntered(new \DateTimeImmutable());
                } else {
                    throw $e;
                }
            }
        }
    }
}
