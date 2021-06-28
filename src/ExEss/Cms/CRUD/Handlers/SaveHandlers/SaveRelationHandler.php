<?php declare(strict_types=1);

namespace ExEss\Cms\CRUD\Handlers\SaveHandlers;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Exception\NotFoundException;
use ExEss\Cms\Component\Flow\Handler\AbstractSaveHandler;
use ExEss\Cms\Component\Flow\Handler\FlowData;
use ExEss\Cms\Component\Flow\SaveFlow;

class SaveRelationHandler extends AbstractSaveHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function shouldHandle(FlowData $data): bool
    {
        return $data->getFlowKey() === SaveFlow::CRUD_NEW_RELATION;
    }

    protected function doHandle(FlowData $data): void
    {
        $model = $data->getModel();

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

        $target = $metadata->getAssociationTargetClass($relationName);
        $reflectionClass = $metadata->getReflectionClass();
        $property = $reflectionClass->getProperty($relationName);
        $property->setAccessible(true);

        /** @var Collection $collection */
        $collection = $property->getValue($parent);
        foreach ($model->getFieldValue('id') as $item) {
            $collection->add($this->em->getRepository($target)->find($item['key'] ?? $item));
        }
        $property->setValue($parent, $collection);

        $this->em->persist($parent);
        $this->em->flush();
    }
}
