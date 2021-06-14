<?php declare(strict_types=1);

namespace ExEss\Cms\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;

class AssociationManager
{
    private ClassMetadataFactory $metadataFactory;

    public function __construct(EntityManager $em)
    {
        $this->metadataFactory = $em->getMetadataFactory();
    }

    /**
     * @return array|string[]
     */
    public function getCollectionValuedAssociationsFor(string $entityName): array
    {
        $metadata = $this->metadataFactory->getMetadataFor($entityName);

        $associations = [];
        foreach ($metadata->getAssociationNames() as $fieldName) {
            if ($metadata->isCollectionValuedAssociation($fieldName)) {
                $associations[$fieldName] = $metadata->getAssociationTargetClass($fieldName);
            }
        }

        return $associations;
    }
}
