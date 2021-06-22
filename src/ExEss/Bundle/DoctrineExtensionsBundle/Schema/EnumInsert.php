<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle\Schema;

use Doctrine\DBAL\Schema\Table;

/**
 * Not added to container
 */
class EnumInsert extends Insert
{
    private Table $table;

    /**
     * @var string[]
     */
    private array $options;

    public function __construct(Table $table, array $options, bool $reverse = false)
    {
        $this->table = $table;
        $this->options = $options;

        if ($reverse) {
            parent::__construct(
                $this->getDelete(),
                $this->getInsert()
            );
        } else {
            parent::__construct(
                $this->getInsert(),
                $this->getDelete()
            );
        }
    }

    private function getInsert(): string
    {
        return \sprintf(
            'insert into %s (id) values (\'%s\');',
            $this->table->getName(),
            \implode('\'), (\'', $this->options)
        );
    }

    private function getDelete(): string
    {
        return \sprintf(
            'delete from %s where id in (\'%s\');',
            $this->table->getName(),
            \implode('\', \'', $this->options)
        );
    }

    /**
     * @param EnumInsert $insert
     */
    public function equals(Insert $insert): bool
    {
        return $this->table->getName() === $insert->table->getName();
    }

    /**
     * @param EnumInsert $insert
     * @return EnumInsert
     */
    public function diff(Insert $insert, bool $reverse = false): ?Insert
    {
        $diff = \array_diff($this->options, $insert->options);
        if (empty($diff)) {
            return null;
        }

        return new self($this->table, $diff, $reverse);
    }
}
