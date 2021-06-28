<?php

namespace ExEss\Cms\Component\Flow\Response\Form;

class ToggleField extends Field
{
    public const TYPE = 'toggle';

    public function __construct(string $id, string $label)
    {
        parent::__construct($id, $label, static::TYPE);
    }
}
