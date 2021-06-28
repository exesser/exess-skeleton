<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Flow\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Service\SelectWithSearchService;
use stdClass;
use ExEss\Cms\Api\V8_Custom\Repository\ListHandler;
use ExEss\Cms\Component\Flow\EnumRecord;
use ExEss\Cms\Component\Flow\EnumRecordFactory;
use ExEss\Cms\Component\Flow\Response;
use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Cms\Logger\Logger;
use ExEss\Cms\Servicemix\Response\SelectableItemInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class EnumFieldBuilder
{
    private ListHandler $listHandler;

    private EnumRecordFactory $enumRecordFactory;

    private Logger $logger;

    private SelectWithSearchService $selectWithSearch;

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        Logger $logger,
        ListHandler $listHandler,
        EnumRecordFactory $enumRecordFactory,
        SelectWithSearchService $selectWithSearchService
    ) {
        $this->listHandler = $listHandler;
        $this->enumRecordFactory = $enumRecordFactory;
        $this->logger = $logger;
        $this->selectWithSearch = $selectWithSearchService;
        $this->em = $em;
    }

    /**
     * This needs the entirely filled model to work!
     */
    public function expandIfConditionalEnum(
        stdClass $field,
        Model $model,
        bool $alwaysFillEnumValues = false
    ): ?array {
        if (
            isset($field->enumValues, $field->type)
            && $field->type === FlowFieldType::FIELD_TYPE_ENUM
            && \is_array($field->enumValues)
            && $this->isConditionalEnum($field->enumValues)
        ) {
            $enumValues = $this->setValuesFromEnumBasedOnModel(
                $model,
                $field,
                $alwaysFillEnumValues
            );
            if (empty($enumValues)) {
                unset($field->enumValues);
            }

            return $enumValues;
        }

        return null;
    }

    public function expandIfSelectWIthSearchBase(stdClass $field, Response $response): ?array
    {
        $model = $response->getModel();

        if (
            isset($field->enumValues, $field->type)
            && $field->type === FlowFieldType::FIELD_TYPE_ENUM
            && \is_array($field->enumValues)
            && $this->isSelectWithSearchEnum($field->enumValues)
        ) {
            $enums = [];
            $selectWithSearch = $field->enumValues[0]->enumValueSource;
            $default = $field->enumValues[0]->default ?? '';
            $result = $this->selectWithSearch->getSelectOptions($selectWithSearch, $model);
            foreach ($result['rows'] as $row) {
                $item = new stdClass();
                $item->key = $row['key'];
                $item->value = $row['label'];
                $enums[] = $item;
            }
            if ($model->getFieldValue($field->id) === '' && $default !== '') {
                $model->setFieldValue($field->id, $default);
            }
            return $enums;
        }
        return null;
    }

    public function expandFixedEnums(array $fieldGroups): void
    {
        foreach ($fieldGroups as $fieldGroup) {
            foreach ($fieldGroup as $field) {
                if (\property_exists($field, 'generateByServer') && $field->generateByServer === true) {
                    if (\property_exists($field, 'module') && \preg_match('~%.+%~', $field->module)) {
                        $retVal = [];
                        $handler = \trim($field->module, '%');
                        $list = $this->listHandler->getList($handler, []);
                        if (\is_array($list['list'])) {
                            foreach ($list['list'] as $external) {
                                if ($external instanceof SelectableItemInterface) {
                                    $row = $this->enumRecordFactory->create(
                                        $external->getDropdownKey(),
                                        null,
                                        $external->getDropdownValue()
                                    );
                                    $retVal[] = $row;
                                }
                            }
                        }

                        $field->enumValues = $retVal;
                        $field->generateByServer = 'done';
                    } elseif (empty($field->enumValues) || !\count((array) $field->enumValues)) { //Classic ENUM
                        $entity = $field->module ?? null;
                        $fieldName = $field->moduleField ?? null;
                        if ($entity && $fieldName) {
                            $enumValues = $this->getEnumRecordsForField($entity, $fieldName);
                        }
                        $field->enumValues = $enumValues ?? [];
                        $field->generateByServer = 'done';
                    }
                }
            }
        }
    }

    public function expandEnums(
        array $formSections,
        Model $model,
        ?string $baseObject = null,
        ?object $baseEntity = null
    ): void {
        foreach ($formSections as $form) {
            foreach ($form as $field) {
                $retVal = [];
                // conditional enums will be handled AFTER the model has been extracted from the form
                if (isset($field->enumValues)
                    && \is_array($field->enumValues)
                    && ($this->isConditionalEnum($field->enumValues)
                        || $this->isSelectWithSearchEnum($field->enumValues)
                        || $this->isDictionaryEnum($field->enumValues)
                    )
                ) {
                    continue;
                }

                if (isset($field->type, $field->generateByServer)
                    && $field->type === 'enum'
                    && $field->generateByServer === true
                ) {
                    if (!isset($field->enumValues)) { //Classic ENUM
                        $retVal = $this->getEnumRecordsForField($field->module, $field->moduleField);
                    } else {
                        $keyLink = \explode('|', $field->enumValues[0]->key);

                        $fieldBaseBean = $baseEntity;
                        if ($fieldBaseBean === null) {
                            $fieldBaseBean = $this->reBaseFlow($baseObject, $keyLink, $model);
                        }

                        $linkedEntities = [];
                        if (\count($keyLink) === 1) {
                            $linkedEntities = $this->em->getRepository($field->module)->findAll();
                        } elseif ($fieldBaseBean) {
                            $linkedEntities = $this->getLinkedEntities($fieldBaseBean, $keyLink);
                        }
                        foreach ($linkedEntities as $linkedEntity) {
                            $property = $this->em
                                ->getClassMetadata(\get_class($linkedEntity))
                                ->getReflectionClass()
                                ->getProperty(\end($keyLink));
                            $property->setAccessible(true);

                            $retVal[] = $this->enumRecordFactory->create(
                                $property->getValue($linkedEntity),
                                null,
                                $this->parseFieldTemplate($linkedEntity, $field->enumValues[0]->value)
                            );
                        }
                    }

                    if (
                        \count($retVal) === 1
                        && empty($model->{$field->id})
                        && $field->auto_select_suggestions === true
                    ) {
                        $model->{$field->id} = $retVal[0]->getKey();
                    }

                    $field->enumValues = $retVal;
                } elseif (isset($field->type, $field->generateByServer, $field->enumValues)
                    && $field->type === 'enum'
                    && $field->generateByServer === false
                    && \is_array($field->enumValues)
                ) {
                    $field->enumValues = $this->getTranslatedEnumValuesFrom(
                        $field->module ?? null,
                        $field->moduleField ?? null,
                        $field->enumValues
                    );
                }
            }
        }
    }

    /**
     * @param array|stdClass[] $values
     * @return array|EnumRecord[]
     */
    protected function getTranslatedEnumValuesFrom(?string $entity, ?string $fieldName, array $values): array
    {
        $listName = null;
        if (!empty($entity) && !empty($fieldName) && $this->em->getMetadataFactory()->hasMetadataFor($entity)) {
            $metadata = $this->em->getClassMetadata($entity);
            $type = $metadata->getTypeOfField($fieldName);
            if (Type::getType($type) instanceof AbstractEnumType) {
                $listName = $type;
            }
        }

        $records = [];
        foreach ($values as $enumKey => $enumValue) {
            $records[$enumKey] = $this->enumRecordFactory->create(
                $enumValue->key,
                $listName,
                $listName ? '' : $enumValue->value
            );
        }

        return $records;
    }

    protected function reBaseFlow(string $baseObject, array &$keyLink, Model $model): ?object
    {
        $baseEntityName = $this->em->getClassMetadata($baseObject)->getAssociationTargetClass(\array_shift($keyLink));
        $baseEntityId = $model->getFieldValue(\strtolower($baseEntityName) . '|id');

        return $this->em->getRepository($baseEntityName)->find($baseEntityId);
    }

    private function parseFieldTemplate(object $entity, string $template): string
    {
        $retVal = '';
        foreach (\explode(' ', $template) as $field) {
            $keyLink = \explode('.', $field); //Get the list of objects by the $key link
            if (\count($keyLink) > 1) {
                $linkedEntity = $this->getLinkedEntities($entity, $keyLink)->first();
                $property = $this->em
                    ->getClassMetadata(\get_class($linkedEntity))
                    ->getReflectionClass()
                    ->getProperty(\end($keyLink));
                $property->setAccessible(true);
                $retVal = $retVal . ' ' . $property->getValue($linkedEntity);
            } else {
                $property = $this->em
                    ->getClassMetadata(\get_class($entity))
                    ->getReflectionClass()
                    ->getProperty($field);
                $property->setAccessible(true);
                $retVal = $retVal . ' ' . $property->getValue($entity);
            }
        }

        return \ltrim($retVal);
    }

    private function getLinkedEntities(object $baseEntity, array $linkArray): ArrayCollection
    {
        $baseEntities = new ArrayCollection([$baseEntity]);
        $linkedEntities = new ArrayCollection();

        foreach ($linkArray as $link) {
            $linkedEntities = new ArrayCollection();

            foreach ($baseEntities as $entity) {
                $metadata = $this->em->getClassMetadata(\get_class($entity));
                if (!$metadata->hasAssociation($link)) {
                    return $baseEntities;
                }

                $property = $metadata->getReflectionClass()->getProperty($link);
                $property->setAccessible(true);

                foreach ($property->getValue($entity) as $value) {
                    $linkedEntities->add($value);
                }
            }

            $baseEntities = $linkedEntities;
        }

        return $linkedEntities;
    }

    /**
     * @return mixed
     */
    private function getDefaultValueFromEnum(array $enumValues, string $type = 'default')
    {
        if (empty($enumValues)) {
            return null;
        }

        if (\count($enumValues) === 1) {
            return \current($enumValues)->key;
        }

        foreach ($enumValues as $enumValue) {
            if (isset($enumValue->$type) && ($enumValue->$type === true || $enumValue->$type === 'true')) {
                return $enumValue->key;
            }
        }

        return null;
    }

    private function isSelectWithSearchEnum(array $enumValues): bool
    {
        return \count($enumValues) === 1 && isset($enumValues[0]->enumValueSource);
    }

    /**
     * @throws \InvalidArgumentException When mixed structure is given, conditional and traditional enums do not mix.
     */
    private function isConditionalEnum(array $enumValues): bool
    {
        $conditional = false;
        foreach ($enumValues as $item) {
            if (!empty($item->condition) && \is_array($item->values)) {
                $conditional = true;
            } else {
                if ($conditional) {
                    throw new \InvalidArgumentException(
                        'invalid enum values given, cannot mix conditional and normal enums'
                    );
                }
            }
        }

        return $conditional;
    }

    private function isDictionaryEnum(array $enumValues): bool
    {
        return \count($enumValues) === 1 && isset(\current($enumValues)->dictionaryValues);
    }

    private function setValuesFromEnumBasedOnModel(
        Model $model,
        stdClass $field,
        bool $alwaysFillEnumValues
    ): array {
        $enumValueConditions = $field->enumValues;

        // by default, none are valid and thus there are no options
        $field->enumValues = [];
        $field->isConditional = true;

        // evaluate and add the valid ones
        try {
            $language = new ExpressionLanguage();
            foreach ($enumValueConditions as $item) {
                if (!isset($item->values)) {
                    continue;
                }

                $values = $this->getTranslatedEnumValuesFrom(
                    $field->module ?? null,
                    $field->moduleField ?? null,
                    $item->values
                );

                // suppress notices
                $errorReporting = \error_reporting(\error_reporting() & ~\E_NOTICE);
                $result = $language->evaluate($item->condition, [
                    'model' => $model->toArray(),
                    'default' => true,
                ]);
                \error_reporting($errorReporting);

                $fieldId = $field->id;
                if ($result) {
                    $matchedConditionKey = Dwp::PREFIX . $fieldId . '|matchedCondition';
                    $oldMatchCondition = $model->$matchedConditionKey ?? '';

                    if ($alwaysFillEnumValues) {
                        // Nextstep mode, always fill the options!
                        $field->enumValues = $values;
                        $field->generateByServer = 'done';
                    }

                    if ($oldMatchCondition !== $item->condition) {
                        //We switched valuesonly update the suggestion and force a new default in this case
                        $field->enumValues = $values;
                        $field->generateByServer = 'done';

                        $defaultValue = $this->getDefaultValueFromEnum($item->values);

                        $overwriteDefaultValue = $this->getDefaultValueFromEnum(
                            $item->values,
                            'overwriteDefault'
                        );

                        $currentValue = $model->offsetExists($fieldId) ? $model->$fieldId : null;

                        $stillValid = $this->isValueStillValid($values, $currentValue);

                        if (!$stillValid || $overwriteDefaultValue) {
                            $model->$fieldId = $overwriteDefaultValue ?? $defaultValue ?? null;
                        }

                        $model->$matchedConditionKey = $item->condition;
                    }

                    // the first valid one is the only one we process
                    return $field->enumValues;
                }
            }
        } catch (\Exception $e) {
            $this->logError($field, $e);
        }

        return $field->enumValues;
    }

    private function isValueStillValid(array $enumValues, ?string $currentValue = null): bool
    {
        if ($currentValue) {
            foreach ($enumValues as $enumValue) {
                if ($enumValue->key === $currentValue) {
                    return true;
                }
            }
        }

        return false;
    }

    private function logError(stdClass $fieldObject, \Exception $e): void
    {
        $this->logger->error(
            \sprintf(
                'Error while calculation dynamic enum: %s with error %s',
                $fieldObject->id,
                $e->getMessage()
            )
        );
    }

    /**
     * @return array|EnumRecord[]
     */
    public function getEnumRecordsForField(string $entity, string $fieldName): array
    {
        $metadata = $this->em->getClassMetadata($entity);
        $type = $metadata->getTypeOfField($fieldName);
        $dbalType = Type::getType($type);
        if (!$dbalType instanceof AbstractEnumType) {
            throw new \InvalidArgumentException("Field $fieldName on $entity is not an enum");
        }

        return $this->getEnumRecordsForType($dbalType, $metadata->fieldMappings['nullable'] ?? false);
    }

    /**
     * @return array|EnumRecord[]
     */
    public function getEnumRecordsForType(AbstractEnumType $type, bool $nullable = false): array
    {
        $enums = [];
        if ($nullable) {
            $enums[] = $this->enumRecordFactory->create(null, $type->getName());
        }
        foreach ($type::getValues() as $value => $item) {
            $enums[] = $this->enumRecordFactory->create($value, $type->getName());
        }

        return $enums;
    }
}
