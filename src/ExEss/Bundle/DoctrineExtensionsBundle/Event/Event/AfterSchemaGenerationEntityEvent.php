<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle\Event\Event;

use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\Mapping\ClassMetadata;
use ExEss\Bundle\DoctrineExtensionsBundle\Schema\Schema;
use Symfony\Contracts\EventDispatcher\Event;

class AfterSchemaGenerationEntityEvent extends Event
{
    public const NAME = 'after_schema.entity_generation';

    private Schema $schema;

    private Table $table;

    private ClassMetadata $classMetadata;

    public function __construct(Schema $schema, Table $table, ClassMetadata $classMetadata)
    {
        $this->schema = $schema;
        $this->table = $table;
        $this->classMetadata = $classMetadata;
    }

    public function getSchema(): Schema
    {
        return $this->schema;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getClassMetadata(): ClassMetadata
    {
        return $this->classMetadata;
    }
}
