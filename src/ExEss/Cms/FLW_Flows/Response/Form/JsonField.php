<?php

namespace ExEss\Cms\FLW_Flows\Response\Form;

class JsonField extends Field
{
    public const TYPE = 'json-editor';

    public function __construct(string $id, string $label)
    {
        parent::__construct($id, $label, static::TYPE);
    }
}
