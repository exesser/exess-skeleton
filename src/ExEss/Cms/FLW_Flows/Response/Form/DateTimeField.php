<?php

namespace ExEss\Cms\FLW_Flows\Response\Form;

class DateTimeField extends Field
{
    public const TYPE = 'datetime';

    public function __construct(string $id, string $label)
    {
        parent::__construct($id, $label, static::TYPE);
    }
}
