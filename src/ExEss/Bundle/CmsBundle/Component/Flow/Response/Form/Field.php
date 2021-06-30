<?php

namespace ExEss\Bundle\CmsBundle\Component\Flow\Response\Form;

abstract class Field extends \stdClass
{
    public string $id;

    public string $label;

    public string $type;

    public bool $required = false;

    public bool $readonly = false;

    public ?string $default = null;

    public \stdClass $expressionProperties;

    public bool $noBackendInteraction = false;

    public function __construct(string $id, string $label, string $type)
    {
        $this->id = $id;
        $this->label = $label;
        $this->type = $type;
        $this->expressionProperties = new \stdClass();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isReadOnly(): bool
    {
        return $this->readonly;
    }

    public function getExpressionProperties(): \stdClass
    {
        return $this->expressionProperties;
    }

    public function setExpressionProperties(\stdClass $expressionProperties): void
    {
        $this->expressionProperties = $expressionProperties;
    }

    public function setNoBackendInteraction(bool $noBackendInteraction): void
    {
        $this->noBackendInteraction = $noBackendInteraction;
    }
}
