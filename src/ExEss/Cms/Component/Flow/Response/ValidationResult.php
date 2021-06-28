<?php
namespace ExEss\Cms\Component\Flow\Response;

use ExEss\Cms\Entity\FlowField;
use JsonSerializable;
use stdClass;

class ValidationResult implements JsonSerializable
{
    private bool $valid = true;

    private array $errors = [];

    private array $fields = [];

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): ValidationResult
    {
        $this->valid = $valid;

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): ValidationResult
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * @return array|FlowField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function jsonSerialize(): stdClass
    {
        return (object)[
            'isValid' => $this->valid,
            'errors' => $this->errors,
        ];
    }
}
