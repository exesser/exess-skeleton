<?php

namespace ExEss\Bundle\CmsBundle\CRUD\Helpers\RecordInfo;

use ExEss\Bundle\CmsBundle\Collection\ObjectCollection;

class RecordInfoCollection extends ObjectCollection
{
    public function __construct()
    {
        parent::__construct(RecordInfo::class);
    }
}
