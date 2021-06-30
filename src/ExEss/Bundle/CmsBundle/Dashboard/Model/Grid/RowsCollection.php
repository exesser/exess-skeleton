<?php
namespace ExEss\Bundle\CmsBundle\Dashboard\Model\Grid;

use ExEss\Bundle\CmsBundle\Collection\ObjectCollection;

class RowsCollection extends ObjectCollection
{
    public function __construct()
    {
        parent::__construct(Row::class);
    }
}
