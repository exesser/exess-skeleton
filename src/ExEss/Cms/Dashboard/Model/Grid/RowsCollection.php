<?php
namespace ExEss\Cms\Dashboard\Model\Grid;

use ExEss\Cms\Collection\ObjectCollection;

class RowsCollection extends ObjectCollection
{
    public function __construct()
    {
        parent::__construct(Row::class);
    }
}
