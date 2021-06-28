<?php

namespace ExEss\Cms\Component\Flow\Response\Form;

class DateField extends Field
{
    public const TYPE = 'date';

    public function __construct(string $id, string $label)
    {
        parent::__construct($id, $label, static::TYPE);
    }
}
