<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Schema;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\SchemaDiff as BaseSchemaDiff;

/**
 * Not added to container
 */
final class SchemaDiff extends BaseSchemaDiff
{
    private BaseSchemaDiff $schemaDiff;

    /**
     * @var Trigger[]
     */
    private array $addedTriggers = [];

    /**
     * @var Trigger[]
     */
    private array $removedTriggers = [];

    /**
     * @var Insert[]
     */
    private array $inserts = [];

    /**
     * @var Insert[]
     */
    private array $reverseInserts = [];

    public function __construct(
        BaseSchemaDiff $schemaDiff,
        array $addedTriggers,
        array $removedTriggers,
        array $inserts,
        array $reverseInserts
    ) {
        $this->schemaDiff = $schemaDiff;
        $this->addedTriggers = $addedTriggers;
        $this->removedTriggers = $removedTriggers;
        $this->inserts = $inserts;
        $this->reverseInserts = $reverseInserts;

        parent::__construct(
            $schemaDiff->newTables,
            $schemaDiff->changedTables,
            $schemaDiff->removedTables,
            $schemaDiff->fromSchema
        );
    }

    /**
     * @return string[]
     */
    public function toSql(AbstractPlatform $platform, bool $down = false): array
    {
        $sql = parent::toSql($platform);

        foreach ($this->removedTriggers as $trigger) {
            $sql[] = $trigger->getSql(true);
        }

        foreach ($this->addedTriggers as $trigger) {
            $sql[] = $trigger->getSql();
        }

        foreach ($this->inserts as $insert) {
            $sql[] = $insert->getSql($down);
        }

        foreach ($this->reverseInserts as $insert) {
            $sql[] = $insert->getSql(!$down);
        }

        return $sql;
    }
}
