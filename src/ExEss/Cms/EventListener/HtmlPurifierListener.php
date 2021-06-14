<?php declare(strict_types=1);

namespace ExEss\Cms\EventListener;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use ExEss\Cms\Cleaner\HtmlCleaner;

class HtmlPurifierListener
{
    private const TYPES_TO_CLEAN = [
        Types::STRING,
        Types::TEXT,
        Types::BLOB,
        Types::BINARY,
    ];

    private HtmlCleaner $cleaner;

    public function __construct(HtmlCleaner $cleaner)
    {
        $this->cleaner = $cleaner;
    }

    public function preFlush(PreFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();

        foreach ($em->getUnitOfWork()->getScheduledEntityInsertions() as $entity) {
            $this->cleanFields($em->getClassMetadata(\get_class($entity)), $entity);
        }
    }

    private function cleanFields(ClassMetadata $metadata, object $entity): void
    {
        $reflectionClass = $metadata->getReflectionClass();

        foreach ($metadata->getFieldNames() as $fieldName) {
            $type = $metadata->getTypeOfField($fieldName);
            if (\in_array($type, self::TYPES_TO_CLEAN, true)) {
                $property = $reflectionClass->getProperty($fieldName);
                $property->setAccessible(true);
                if (!empty($value = $property->getValue($entity))) {
                    $property->setValue($entity, $this->cleaner->cleanHtml($value));
                }
            }
        }
    }
}
