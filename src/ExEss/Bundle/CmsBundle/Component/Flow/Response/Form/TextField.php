<?php

namespace ExEss\Bundle\CmsBundle\Component\Flow\Response\Form;

class TextField extends Field
{
    public const TYPE = 'TextField';

    public function __construct(string $id, string $label)
    {
        parent::__construct($id, $label, static::TYPE);
    }
}
