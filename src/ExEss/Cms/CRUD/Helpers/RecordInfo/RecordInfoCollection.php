<?php

namespace ExEss\Cms\CRUD\Helpers\RecordInfo;

use ExEss\Cms\Collection\ObjectCollection;

class RecordInfoCollection extends ObjectCollection
{
    public function __construct()
    {
        parent::__construct(RecordInfo::class);
    }
}
