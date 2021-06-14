<?php

namespace ExEss\Cms\CRUD\Helpers\RecordInfo;

class FieldInfo implements \JsonSerializable
{
    private ?array $enumValues = null;

    private string $name;

    private string $type;

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function addEnumValues(array $enums): void
    {
        $this->enumValues = $enums;
    }

    public function jsonSerialize(): array
    {
        $data = [
            'name' => $this->name,
            'type' => $this->type,
        ];

        if ($this->enumValues) {
            $data['enumValues'] = $this->enumValues;
        }

        return $data;
    }
}
