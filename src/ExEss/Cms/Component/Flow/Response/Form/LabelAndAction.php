<?php

namespace ExEss\Cms\Component\Flow\Response\Form;

class LabelAndAction extends Field
{
    public const TYPE ='LabelAndAction';

    public array $action;

    public ?string $fieldExpression;

    public function __construct(string $id, string $label, array $action)
    {
        parent::__construct($id, $label, static::TYPE);
        $this->action = $action;
    }

    public function setFieldExpression(?string $string): void
    {
        $this->fieldExpression = $string;
    }
}
