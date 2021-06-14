<?php
namespace ExEss\Cms\Parser\Query;

class Join
{
    private string $alias;

    private string $query;

    private string $fatEntityKey;

    public function __construct(string $query, string $alias, string $fatEntityKey)
    {
        $this->query = $query;
        $this->alias = $alias;
        $this->fatEntityKey = $fatEntityKey;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getFatEntityKey(): string
    {
        return $this->fatEntityKey;
    }

    public function getFieldAlias(string $field): string
    {
        return \preg_replace('/[^a-zA-Z0-9_]+/', '_', $this->fatEntityKey . '|' . $field);
    }

    public function __toString(): string
    {
        return $this->query;
    }
}
