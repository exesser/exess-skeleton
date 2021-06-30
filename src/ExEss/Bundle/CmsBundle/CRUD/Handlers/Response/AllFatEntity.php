<?php

namespace ExEss\Bundle\CmsBundle\CRUD\Handlers\Response;

class AllFatEntity
{
    private string $recordType1;
    private string $recordType2;
    private string $recordType3;

    public function __construct(string $recordType1, string $recordType2, string $recordType3)
    {
        $this->recordType1 = $recordType1;
        $this->recordType2 = $recordType2;
        $this->recordType3 = $recordType3;
    }

    public function getRecordType1(): string
    {
        return $this->recordType1;
    }

    public function getRecordType2(): string
    {
        return $this->recordType2;
    }

    public function getRecordType3(): string
    {
        return $this->recordType3;
    }
}
