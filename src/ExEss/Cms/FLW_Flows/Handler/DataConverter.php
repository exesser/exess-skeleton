<?php declare(strict_types=1);

namespace ExEss\Cms\FLW_Flows\Handler;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Collection\ObjectCollection;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Entity\User;
use ExEss\Cms\FLW_Flows\Response\Model;

class DataConverter extends AbstractSaveHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public static function shouldHandle(FlowData $data): bool
    {
        $flow = $data->getFlow();
        return $flow->getBaseObject() !== null
            && !$flow->isExternal()
        ;
    }

    protected function doHandle(FlowData $data): void
    {
        $this->convertToEntities(
            $data->getModel(),
            $data->getFlow()->getIsConfig(),
            $data->getConvertedModel(),
            $data->getEntities(),
        );

        $this->em->flush();
    }

    private function setIdPathIfNeeded(Model $model, object $entity, string $idPath): void
    {
        $module = \get_class($entity);
        if (
            (
                !$model->offsetExists($idPath)
                || !$model->hasNonEmptyValueFor($idPath, true)
            )
            && !(
                $model->offsetExists('recordTypeOfRecordId')
                && $model->offsetExists('baseModule')
                && $module === $model->offsetGet('baseModule')
                && $module !== $model->offsetGet('recordTypeOfRecordId')
            )
        ) {
            $model->offsetSet($idPath, $entity->getId());
        }
    }

    private function convertToEntities(
        Model $model,
        bool $configFlow,
        array $explodedModel,
        ObjectCollection $entities,
        array $parentBeans = [],
        array $path = [],
        bool $subPath = false,
        bool $whereClause = false
    ): void {
        $newEntities = [];
        if (empty($explodedModel['baseModule'])) {
            return;
        }
        $baseModule = $explodedModel['baseModule'];
        $metadata = $this->em->getClassMetadata($baseModule);

        foreach ($explodedModel as $relationship => $fields) {
            $nestedEntities = [];
            $hasMultipleRecords = false;

            if ($relationship === 'baseModule' || $relationship === Dwp::DWP) {
                continue;
            }

            if (!$subPath && $relationship !== $baseModule) {
                $path = [$relationship];
            } elseif ($subPath && !$whereClause) {
                $path[] = $relationship;
            } elseif (!$whereClause) {
                $path = [];
            }

            if (\is_array($fields)) {
                $nestedEntities = \array_filter(
                    $fields,
                    function ($element) {
                        return \is_array($element) && !empty($element);
                    }
                );
                $fieldsForRel = $fields;

                //check if the fields only carry a list of records (having numeric keys) so we can save all those
                if ($fields === \array_values($fields)) {
                    $hasMultipleRecords = true;
                    $nestedEntities = [];
                }

                $fields = \array_filter(
                    $fields,
                    function ($element) {
                        return !\is_array($element);
                    }
                );
            }

            $returnedFatEntities = [];

            $moduleName = null;
            if ($relationship === $baseModule) {
                $moduleName = $baseModule;
            } elseif ($metadata->hasAssociation($relationship)) {
                $moduleName = $metadata->getAssociationTargetClass($relationship);
            }

            if (!\is_null($moduleName)) {
                $recordList = [];

                $entities[$moduleName] = $entities[$moduleName] ?? new ObjectCollection($moduleName);

                // Find the first fat entity from the $baseModuletype
                $baseEntity = isset($entities[$baseModule])
                    ? ($entities[$baseModule]->current() ?? null)
                    : null;

                if ($hasMultipleRecords) {
                    $recordList = $fieldsForRel;
                } else {
                    $recordList[] = $fields;
                }

                if (\is_array($recordList)
                    && (\count($nestedEntities) === 0
                        || (
                            isset(\array_keys($nestedEntities)[0][0])
                            && \array_keys($nestedEntities)[0][0] != '('
                        )
                    )
                ) {
                    foreach ($recordList as $record) {
                        $createdEntity = $this->fatEntityConvertDefault(
                            $moduleName,
                            $record,
                            $configFlow
                        );
                        if (!$createdEntity) {
                            continue;
                        }

                        if ($baseEntity !== null && !empty($createdEntity->getId())) {
                            $reflectionClass = $metadata->getReflectionClass();
                            $property = $reflectionClass->getProperty($relationship);
                            $property->setAccessible(true);
                            if ($metadata->isCollectionValuedAssociation($relationship)) {
                                /** @var Collection $collection */
                                $collection = $property->getValue($baseEntity);
                                $collection->add($createdEntity);
                                $property->setValue($baseEntity, $collection);
                            } else {
                                $property->setValue($baseEntity, $createdEntity);
                            }
                        }

                        $this->setIdPathIfNeeded(
                            $model,
                            $createdEntity,
                            !empty($path) ? \implode('|', \array_unique($path)) . '|id': 'id'
                        );
                        $entities[$moduleName][] = $createdEntity;
                        $newEntities[] = $createdEntity;
                    }
                }

                foreach ($nestedEntities as $nestedEntityKey => $nestedEntityFields) {
                    if ($nestedEntityKey[0] === '(') { //This is actually not a child, but a sibling
                        //this is a where clause
                        $key = \array_pop($path);
                        $path[$key . $nestedEntityKey] = $key . $nestedEntityKey;
                        $this->convertToEntities(
                            $model,
                            $configFlow,
                            [$relationship => $nestedEntityFields, 'baseModule' => $baseModule],
                            $entities,
                            $parentBeans,
                            $path,
                            true,
                            true
                        );
                        unset($path[$key . $nestedEntityKey]);
                        $path[] = $key;
                    } else {
                        $this->convertToEntities(
                            $model,
                            $configFlow,
                            [$nestedEntityKey => $nestedEntityFields, 'baseModule' => $moduleName],
                            $entities,
                            $returnedFatEntities,
                            $path,
                            true
                        );
                    }
                }
            }

            if ($relationship === 'rel' && !empty($fieldsForRel)) {
                $baseEntity = isset($entities[$baseModule]) ? $entities[$baseModule]->current() : null;
                if ($baseEntity) {
                    $reflectionClass = $metadata->getReflectionClass();
                    foreach ($fieldsForRel as $fieldRelationship => $fieldValues) {
                        $target = $metadata->getAssociationTargetClass($fieldRelationship);

                        $property = $reflectionClass->getProperty($fieldRelationship);
                        $property->setAccessible(true);

                        if ($metadata->isCollectionValuedAssociation($fieldRelationship)) {
                            /** @var Collection $collection */
                            $collection = $property->getValue($baseEntity);
                            $collection->clear();
                            foreach ((array) $fieldValues as $fieldValue) {
                                $collection->add(
                                    $this->em->getRepository($target)->find($fieldValue['key'] ?? $fieldValue)
                                );
                            }
                            $property->setValue($baseEntity, $collection);
                        } else {
                            $property->setValue(
                                $baseEntity,
                                $this->em->getRepository($target)->find($fieldValue['key'] ?? $fieldValue)
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Default fat entity load/create new function will load the fat entity/create
     * the fat entity and set all fields and save.
     *
     * @throws \Exception Could not find existing fat entity.
     */
    private function fatEntityConvertDefault(
        string $moduleName,
        array $fields,
        bool $configFlow = false
    ): ?object {

        if (!empty($fields['id'])) {
            $entity = $this->em->getRepository($moduleName)->find($fields['id']);
        } elseif (\array_key_exists('NO_INSERT', $fields)) {
            return null;
        } else {
            $entity = new $moduleName;
            // @todo remove when this is automatically done
            $entity->setCreatedBy($this->em->getRepository(User::class)->find('1'));
        }

        $metadata = $this->em->getClassMetadata($moduleName);

        if (!empty($fields)) {
            $reflectionClass = $metadata->getReflectionClass();
            foreach ($fields as $fieldName => $value) {
                if (!$metadata->hasField($fieldName)) {
                    // @todo we might want to throw an exception here
                    continue;
                }
                if ($fieldName === 'id' && $value === null) {
                    continue;
                }

                if (!empty($value) && \is_string($value)) {
                    switch ($metadata->getTypeOfField($fieldName)) {
                        case 'double':
                        case 'decimal':
                        case 'float':
                            $value = (float) $value;
                            break;
                        case 'uint':
                        case 'ulong':
                        case 'long':
                        case 'short':
                        case 'tinyint':
                        case 'int':
                            $value = (int) $value;
                            break;
                        case 'json':
                            // @todo if json-editor sends us an array here instead of a string we can remove it
                            if (\is_string($value)) {
                                $value = \json_decode($value, true);
                            }
                            break;
                        default:
                            break;
                    }
                }

                $property = $reflectionClass->getProperty($fieldName);
                $property->setAccessible(true);
                $property->setValue($entity, $value);
            }

            // @todo reimplement
            // if (!$metadata->has(Config::class) || $configFlow) {
                $this->em->persist($entity);
            // }
        } else {
            // If a new fat entity was not saved previously a relation on this fat entity can never be created
            // => save fat entity
            $this->em->persist($entity);
        }

        return $entity;
    }
}
