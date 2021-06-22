<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle\Schema;

use Doctrine\DBAL\Schema\AbstractAsset;

/**
 * Not added to container
 */
class Insert extends AbstractAsset
{
    private string $toSql;

    private string $fromSql;

    public function __construct(string $toSql, string $fromSql)
    {
        $this->toSql = $toSql;
        $this->fromSql = $fromSql;
    }

    public function getSql(bool $down): string
    {
        return $down? $this->getFromSql(): $this->getToSql();
    }

    public function getToSql(): string
    {
        return $this->toSql;
    }

    public function getFromSql(): string
    {
        return $this->fromSql;
    }

    public function equals(Insert $insert): bool
    {
        return $this->toSql === $insert->fromSql;
    }

    public function diff(Insert $insert, bool $reverse = false): ?Insert
    {
        if ($reverse) {
            return new static($this->toSql, $this->fromSql);
        }
        return new static($this->fromSql, $this->toSql);
    }
}
