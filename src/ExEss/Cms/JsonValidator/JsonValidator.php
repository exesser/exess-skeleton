<?php

namespace ExEss\Cms\JsonValidator;

use JsonSchema\Validator;
use stdClass;

class JsonValidator
{
    private Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * @param mixed $data
     */
    public function validate($data, stdClass $schema): JsonValidationResult
    {
        $this->validator->reset();
        $data = $this->prepareData($data);
        $this->validator->validate($data, $schema);
        return new JsonValidationResult($data, $this->validator->getErrors());
    }

    /**
     * @param mixed $data
     */
    public function validateAndCoerce($data, stdClass $schema): JsonValidationResult
    {
        $this->validator->reset();
        $data = $this->prepareData($data);
        $this->validator->coerce($data, $schema);
        return new JsonValidationResult($data, $this->validator->getErrors());
    }

    /**
     * @param mixed $data
     */
    public function prepareData($data): stdClass
    {
        if (\is_object($data)) {
            return $data;
        }

        if (\is_string($data)) {
            return $this->prepareJson($data);
        }

        if (\is_array($data)) {
            return \json_decode(\json_encode($data));
        }

        throw new \InvalidArgumentException("data of type '".\gettype($data)."' can not be validated");
    }

    public function prepareJson(string $json): stdClass
    {
        $data = \json_decode($json);
        if (!$data instanceof stdClass) {
            throw new \InvalidArgumentException("json must represent an object");
        }
        return $data;
    }

    /**
     * @param mixed $schemaData
     */
    public function prepareSchema($schemaData): stdClass
    {
        if (\is_object($schemaData)) {
            return $schemaData;
        }

        if (\is_array($schemaData)) {
            return \json_decode(\json_encode($schemaData));
        }

        throw new \InvalidArgumentException("data of type '".\gettype($schemaData)."' is not a valid schema");
    }

    public function prepareSchemaFromLocalFile(string $path): stdClass
    {
        if (!\file_exists($path)) {
            throw new \RuntimeException("schema '$path' not found");
        }

        return $this->prepareSchemaFromURI("file://$path");
    }

    public function prepareSchemaFromURI(string $uri): stdClass
    {
        return (object)['$ref' => $uri];
    }
}
