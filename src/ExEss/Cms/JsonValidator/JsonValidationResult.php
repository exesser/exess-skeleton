<?php

namespace ExEss\Cms\JsonValidator;

use stdClass;

class JsonValidationResult
{
    private stdClass $data;

    private array $errors;

    public function __construct(stdClass $data, array $errors)
    {
        $this->data = $data;
        $this->errors = $errors;
    }

    public function getData(): stdClass
    {
        return $this->data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function isValid(): bool
    {
        return \count($this->errors) === 0;
    }
}
