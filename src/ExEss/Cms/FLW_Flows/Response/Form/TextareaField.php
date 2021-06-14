<?php

namespace ExEss\Cms\FLW_Flows\Response\Form;

class TextareaField extends Field
{
    public const TYPE = 'textarea';

    public function __construct(string $id, string $label)
    {
        parent::__construct($id, $label, static::TYPE);
    }
}
