<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\CRUD\BackendCommand;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\Component\Core\Flow\Action\BackendCommandInterface;
use ExEss\Bundle\CmsBundle\Dictionary\Model\Dwp;
use ExEss\Bundle\CmsBundle\Exception\NotFoundException;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;

class CrudDeleteRelationshipCommand implements BackendCommandInterface
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
