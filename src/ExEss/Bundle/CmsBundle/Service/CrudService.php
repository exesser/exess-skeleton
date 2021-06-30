<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\CRUD\Helpers\RecordInfo\FieldInfo;
use ExEss\Bundle\CmsBundle\CRUD\Helpers\RecordInfo\RecordInfo;
use ExEss\Bundle\CmsBundle\CRUD\Helpers\RecordInfo\RecordInfoCollection;
use ExEss\Bundle\CmsBundle\CRUD\Helpers\RecordInfo\RelationInfo;
use ExEss\Bundle\CmsBundle\Component\Flow\Builder\EnumFieldBuilder;

class CrudService
{
    private EnumFieldBuilder $enumFieldBuilder;

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        EnumFieldBuilder $enumFieldBuilder
    ) {
        $this->enumFieldBuilder = $enumFieldBuilder;
        $this->em = $em;
    }

    public function getRecordsInformation(): RecordInfoCollection
    {
        $records = new RecordInfoCollection();

        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $metadata) {
            $records[$metadata->getName()] = $recordInfo = new RecordInfo($metadata->getName());

            foreach ($metadata->getAssociationNames() as $fieldName) {
                $recordInfo->addRelation(new RelationInfo(
                    $fieldName,
                    $metadata->getAssociationTargetClass($fieldName),
                    $metadata->isCollectionValuedAssociation($fieldName)
                ));
            }

            foreach ($metadata->getFieldNames() as $fieldName) {
                try {
                    // check if it's an enum
                    $enumValues = $this->enumFieldBuilder->getEnumRecordsForField($metadata->getName(), $fieldName);
                    $field = new FieldInfo($fieldName, 'enum');
                    $field->addEnumValues($enumValues);
                } catch (\InvalidArgumentException $e) {
                    // regular field
                    $field = new FieldInfo($fieldName, $metadata->getTypeOfField($fieldName));
                }
                $recordInfo->addField($field);
            }
        }

        return $records;
    }
}
