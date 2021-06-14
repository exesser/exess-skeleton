<?php

namespace ExEss\Cms\CRUD\Helpers\RecordInfo;

class RelationInfo implements \JsonSerializable
{

    public const TYPE_SINGLE = 'SINGLE';
    public const TYPE_MULTI = 'MULTI';

    private string $name;

    private bool $multiRelation;

    private string $record;

    public function __construct(string $name, string $record, bool $multiRelation)
    {
        $this->name = $name;
        $this->record = $record;
        $this->multiRelation = $multiRelation;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'multiRelation' => $this->multiRelation,
            'record' => $this->record,
        ];
    }
}
