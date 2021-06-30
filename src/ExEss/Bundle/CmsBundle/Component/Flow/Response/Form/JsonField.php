<?php

namespace ExEss\Bundle\CmsBundle\Component\Flow\Response\Form;

class JsonField extends Field
{
    public const TYPE = 'json-editor';

    public function __construct(string $id, string $label)
    {
        parent::__construct($id, $label, static::TYPE);
    }
}
