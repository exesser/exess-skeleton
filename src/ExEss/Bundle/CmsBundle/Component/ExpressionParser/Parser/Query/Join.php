<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Query;

class Join
{
    private string $alias;

    private string $query;

    private string $entityKey;

    public function __construct(string $query, string $alias, string $entityKey)
    {
        $this->query = $query;
        $this->alias = $alias;
        $this->entityKey = $entityKey;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getEntityKey(): string
    {
        return $this->entityKey;
    }

    public function getFieldAlias(string $field): string
    {
        return \preg_replace('/[^a-zA-Z0-9_]+/', '_', $this->entityKey . '|' . $field);
    }

    public function __toString(): string
    {
        return $this->query;
    }
}
