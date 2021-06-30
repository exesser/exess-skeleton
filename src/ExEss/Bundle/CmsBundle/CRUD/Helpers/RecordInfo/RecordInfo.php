<?php

namespace ExEss\Bundle\CmsBundle\CRUD\Helpers\RecordInfo;

class RecordInfo implements \JsonSerializable
{
    /**
     * @var FieldInfo[]
     */
    private array $fields = [];

    /**
     * @var RelationInfo[]
     */
    private array $relations = [];

    private string $recordName;

    public function __construct(string $recordName)
    {
        $this->recordName = $recordName;
    }

    public function addRelation(RelationInfo $relation): void
    {
        $this->relations[] = $relation;
    }

    public function addField(FieldInfo $field): void
    {
        $this->fields[] = $field;
    }

    public function jsonSerialize(): array
    {
        return [
            'recordName' => $this->recordName,
            'relations' => $this->relations,
            'fields' => $this->fields,
        ];
    }
}
