<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle\Event\Event;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use ExEss\Bundle\DoctrineExtensionsBundle\Schema\Schema;
use Symfony\Contracts\EventDispatcher\Event;

class AfterSchemaGenerationColumnEvent extends Event
{
    public const NAME = 'after_schema.column_generation';

    private Schema $schema;

    private Table $table;

    private Column $column;

    public function __construct(Schema $schema, Table $table, Column $column)
    {
        $this->schema = $schema;
        $this->table = $table;
        $this->column = $column;
    }

    public function getSchema(): Schema
    {
        return $this->schema;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getColumn(): Column
    {
        return $this->column;
    }
}
