<?php

namespace ExEss\Cms\ListFunctions\HelperClasses;

class DynamicListRowCell
{
    public ?string $type;

    public string $class;

    public \stdClass $options;

    public \stdClass $cellLines;

    public function __construct()
    {
        $this->options = new \stdClass();
        $this->cellLines = new \stdClass();
    }
}
