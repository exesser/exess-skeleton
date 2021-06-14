<?php

namespace ExEss\Cms\FLW_Flows\Response\Form;

class DateField extends Field
{
    public const TYPE = 'date';

    public function __construct(string $id, string $label)
    {
        parent::__construct($id, $label, static::TYPE);
    }
}
