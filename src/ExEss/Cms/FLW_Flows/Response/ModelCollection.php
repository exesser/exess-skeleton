<?php

namespace ExEss\Cms\FLW_Flows\Response;

use ExEss\Cms\Collection\ObjectCollection;

class ModelCollection extends ObjectCollection
{
    public function __construct()
    {
        parent::__construct(Model::class);
    }
}
