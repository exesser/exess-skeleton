<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle\Schema;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Schema as BaseSchema;
use Doctrine\DBAL\Schema\Table;

/**
 * Not added to container
 */
final class Schema extends BaseSchema
{
    /**
     * @var Trigger[]
     */
    private array $triggers = [];

    /**
     * @var Insert[]
     */
    private array $inserts = [];

    public function __construct(BaseSchema $schema)
    {
        parent::__construct(
            $schema->getTables(),
            $schema->getSequences(),
            $schema->_schemaConfig,
            $schema->getNamespaces()
        );
    }

    /**
     * @throws \Doctrine\DBAL\DBALException When the operation or tablename is incorrect
     */
    public function createTrigger(
        string $operation,
        string $timing,
        Table $table,
        string $auditTableName,
        ?string $statement = null
    ): void {
        $trigger = new Trigger($operation, $timing, $table, $auditTableName, $statement);
        $this->triggers[$trigger->getName()] = $trigger;
    }

    public function getTriggers(): array
    {
        return $this->triggers;
    }

    public function addInsert(Insert $insert): void
    {
        $this->inserts[] = $insert;
    }

    /**
     * @return Insert[]
     */
    public function getInserts(): array
    {
        return $this->inserts;
    }

    public function getMigrateToSql(BaseSchema $toSchema, AbstractPlatform $platform): array
    {
        $comparator = new Comparator();
        $schemaDiff = $comparator->compare($this, $toSchema);

        return $schemaDiff->toSql($platform);
    }

    public function getMigrateFromSql(BaseSchema $fromSchema, AbstractPlatform $platform): array
    {
        $comparator = new Comparator(true);
        $schemaDiff = $comparator->compare($fromSchema, $this);

        return $schemaDiff->toSql($platform, true);
    }
}
