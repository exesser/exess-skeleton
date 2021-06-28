<?php declare(strict_types=1);

namespace ExEss\Cms\CRUD\BackendCommand;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Exception\NotFoundException;
use ExEss\Cms\Component\Flow\Action\BackendCommand\BackendCommand;
use ExEss\Cms\Component\Flow\Response\Model;

class CrudDeleteRelationshipCommand implements BackendCommand
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function execute(array $recordIds, ?Model $model = null): void
    {
        $parentId = $model->getFieldValue(Dwp::PARENT_ID);
        $parentType = $model->getFieldValue(Dwp::PARENT_TYPE);
        $relationName = $model->getFieldValue(Dwp::RELATION_NAME);

        $parent = $this->em->getRepository($parentType)->find($parentId);
        if (!$parent) {
            throw new NotFoundException("No $parentType with id $parentId found");
        }

        $metadata = $this->em->getClassMetadata($parentType);
        if (!$metadata->hasAssociation($relationName)) {
            throw new \InvalidArgumentException("$parentType has no association $relationName");
        }

        if (!$metadata->isCollectionValuedAssociation($relationName)) {
            throw new \InvalidArgumentException("$relationName on $parentType doesn't allow multiple values");
        }

        $reflectionClass = $metadata->getReflectionClass();
        $property = $reflectionClass->getProperty($relationName);
        $property->setAccessible(true);

        /** @var Collection $collection */
        $collection = $property->getValue($parent);
        foreach ($collection as $item) {
            if (\in_array($item->getId(), $recordIds, true)) {
                $collection->removeElement($item);
            }
        }
        $property->setValue($parent, $collection);

        $this->em->persist($parent);
        $this->em->flush();
    }
}
