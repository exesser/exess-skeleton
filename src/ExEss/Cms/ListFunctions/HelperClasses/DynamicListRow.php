<?php

namespace ExEss\Cms\ListFunctions\HelperClasses;

class DynamicListRow
{
    public ?string $id = 'NO_ID_FROM_API_RECEIVED';

    public array $cells = [];

    public string $createDate;

    /**
     * @var array|\stdClass
     */
    public $rowData;

    public ?string $sortByOrder = null;

    public ?string $sortBy = null;

    public function __construct()
    {
        $this->createDate = \microtime();
    }
}
