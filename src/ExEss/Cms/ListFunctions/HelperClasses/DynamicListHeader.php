<?php

namespace ExEss\Cms\ListFunctions\HelperClasses;

class DynamicListHeader
{
    public string $label;

    public ?int $colSize;

    public ?string $cellType;

    public \stdClass $cellLines;

    public function __construct()
    {
        $this->cellLines = new \stdClass();
    }
}
