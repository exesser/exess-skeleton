<?php

namespace ExEss\Cms\FLW_Flows\Response\Form;

class ToggleField extends Field
{
    public const TYPE = 'toggle';

    public function __construct(string $id, string $label)
    {
        parent::__construct($id, $label, static::TYPE);
    }
}
