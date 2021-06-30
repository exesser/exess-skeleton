<?php

namespace ExEss\Bundle\CmsBundle\Component\Flow\Response\Form;

class TextareaField extends Field
{
    public const TYPE = 'textarea';

    public function __construct(string $id, string $label)
    {
        parent::__construct($id, $label, static::TYPE);
    }
}
