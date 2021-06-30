<?php

namespace ExEss\Bundle\CmsBundle\CRUD\Factory;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Proxy;
use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;
use ExEss\Bundle\CmsBundle\CRUD\Config\CrudMetadata;
use ExEss\Bundle\CmsBundle\Dictionary\Format;
use ExEss\Bundle\CmsBundle\Exception\NotFoundException;
use ExEss\Bundle\CmsBundle\Dictionary\Model\Dwp;
use ExEss\Bundle\CmsBundle\Component\Flow\EnumRecordFactory;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Form\DateField;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Form\DateTimeField;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Form\EnumField;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Form\Field;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Form\JsonField;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Form\LabelAndAction;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Form\SelectWithSearchField;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Form\TextareaField;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Form\TextField;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Form\ToggleField;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Service\SelectWithSearchService;

class FieldFactory
{
    private const ACTION_ID = 'crud_label_action';
    private const DASHBOARD_ID = 'CrudRecordView';

    /**
     * Fields that will not be displayed in readonly
     */
    private const UNUSED_FIELDS = ['userHash'];

    /**
     * Fields that should not be displayed if view is not read.
     */
    public const READ_ONLY_FIELDS = ['id', 'dateEntered', 'dateModified', 'modifiedUser', 'createdBy'];

    private SelectWithSearchService $selectWithSearchService;

    private EntityManager $em;

    private EnumRecordFactory $enumRecordFactory;

    public function __construct(
        EntityManager $em,
        EnumRecordFactory $enumRecordFactory,
        SelectWithSearchService $selectWithSearchService
    ) {
        $this->selectWithSearchService = $selectWithSearchService;
        $this->em = $em;
        $this->enumRecordFactory = $enumRecordFactory;
    }

    public function makeAssociationField(
        string $property,
        ClassMetadata $metadata,
        object $entity,
        bool $readOnly
    ): ?Field {
        $label = \ucfirst(\str_replace('_', ' ', $property));
        if (
            \in_array($property, self::UNUSED_FIELDS, true)
            || (!$readOnly && \in_array($property, self::READ_ONLY_FIELDS, true))
        ) {
            return null;
        }

        $value = $this->getFieldValue($metadata, $entity, $property);
        $targetClass = $metadata->getAssociationTargetClass($property);

        if ($value && $readOnly) {
            if ($value instanceof Proxy) {
                $value->__load();
            }
            $field = new LabelAndAction(
                $property,
                $label,
                [
                    "id" => self::ACTION_ID,
                    "params" => ["dashboardId" => self::DASHBOARD_ID],
                    "recordType" => $targetClass,
                    "recordId" => $value->getId(),
                ]
            );
            $field->setFieldExpression(
                CrudMetadata::getCrudListC1R1($this->em->getClassMetadata($targetClass), $value)
            );
        } elseif (!$readOnly) {
            $field = new SelectWithSearchField("$property|id", $label, 'crud_relate_relationship');
            $field->setParams([
                "baseObject" => $targetClass,
            ]);
        } else {
            $field = new LabelAndAction(
                $property,
                $label,
                []
            );
        }

        $field->setNoBackendInteraction(true);
        $field->readonly = $readOnly;

        return $field;
    }

    public function makeField(
        string $property,
        ClassMetadata $metadata,
        bool $readOnly
    ): ?Field {
        $label = \ucfirst(\str_replace('_', ' ', $property));
        if (
            \in_array($property, self::UNUSED_FIELDS, true)
            || (!$readOnly && \in_array($property, self::READ_ONLY_FIELDS, true))
        ) {
            return null;
        }

        $mapping = $metadata->getFieldMapping($property);
        $type = $metadata->getTypeOfField($property);
        $dbalType = \Doctrine\DBAL\Types\Type::getType($type);

        if ($dbalType instanceof AbstractEnumType) {
            $options = [];
            if ($mapping['nullable'] === true) {
                $options[] = $this->enumRecordFactory->create(null, $type);
            }
            foreach ($dbalType->getValues() as $key => $value) {
                $options[] = $this->enumRecordFactory->create($key, $type, $value);
            }
            $field = new EnumField($property, $label, $options);
        } else {
            switch ($type) {
                case Types::BOOLEAN:
                    $field = new ToggleField($property, $label);
                    break;
                case Types::JSON:
                    $field = new JsonField($property, $label);
                    break;
                case Types::TEXT:
                    $field = new TextareaField($property, $label);
                    break;
                case Types::FLOAT:
                case Types::INTEGER:
                case Types::STRING:
                    $field = new TextField($property, $label);
                    break;
                case Types::DATETIME_MUTABLE:
                    $field = new DateTimeField($property, $label);
                    break;
                case Types::DATE_MUTABLE:
                    $field = new DateField($property, $label);
                    break;
                default:
                    throw new NotFoundException("Unknown field type $type");
            }
        }

        $field->setNoBackendInteraction(true);
        $field->readonly = $readOnly;

        return $field;
    }

    public function setValueOnModel(Field $field, Model $model, ClassMetadata $metadata, object $entity): void
    {
        if ($field instanceof LabelAndAction) {
            return;
        }

        $isDuplicate = $model->hasNonEmptyValueFor(Dwp::CRUD_DUPLICATE_RECORD_ID);

        $property = $field->getId();

        if ($field instanceof SelectWithSearchField) {
            $parts = \explode('|', $property);
            $value = $this->getFieldValue($metadata, $entity, $parts[0]);
            if ($value) {
                $value = $this->selectWithSearchService->getLabelsForValues(
                    $field->getDatasourceName(),
                    [$value->getId()],
                    null,
                    $field->getParams()['baseObject'] ?? null
                );
            }
        } else {
            $value = $this->getFieldValue($metadata, $entity, $property);

            if ($value instanceof \DateTimeInterface) {
                $value = $value->format(
                    $field instanceof DateField ? Format::DB_DATE_FORMAT : Format::DB_DATETIME_FORMAT
                );
            }

            if ($isDuplicate) {
                if (\in_array($property, $metadata->getIdentifierFieldNames(), true)) {
                    $model->offsetUnset($property);
                    return;
                }
                if (\in_array($property, CrudMetadata::getMakeUniqueOnDuplicateFields($metadata->getName()), true)) {
                    $value .= '::copy' . \date('YmdHis');
                }
            }
        }

        $model->setFieldValue($property, $value);
    }

    /**
     * @return mixed
     */
    private function getFieldValue(ClassMetadata $metadata, object $entity, string $field)
    {
        if ($entity instanceof Proxy) {
            $entity->__load();
        }

        $class = $metadata->getReflectionClass();

        $property = $class->getProperty($field);
        $property->setAccessible(true);

        return $property->getValue($entity);
    }
}
