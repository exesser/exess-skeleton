<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Schema;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\AbstractAsset;
use Doctrine\DBAL\Schema\Table;
use ExEss\Cms\Component\Doctrine\Type\AuditOperationEnumType;

/**
 * Not added to container
 */
final class Trigger extends AbstractAsset
{
    private string $operation;

    private string $timing;

    private Table $table;

    private string $auditTableName;

    private ?string $currentStatement;

    private string $triggerName;

    private static string $createTriggerTemplate = <<<SQL
CREATE TRIGGER {{triggerName}}
  {{timing}} {{operation}} ON {{tableName}}
  FOR EACH ROW {{statement}};
SQL;

    private static string $statementTemplate = <<<SQL
BEGIN
  INSERT INTO {{auditTable}}
    SELECT now(6), '{{operation}}', {{tableName}}.* 
    FROM {{tableName}} 
    WHERE {{primaryKeys}};
END
SQL;

    private static string $primaryKeysTemplate = "{{primaryKey}} = {{previous}}.{{primaryKey}}";

    public function __construct(
        string $operation,
        string $timing,
        Table $table,
        string $auditTableName,
        ?string $currentStatement
    ) {
        if (!\in_array($operation, AuditOperationEnumType::getValues(), true)) {
            throw new DBALException('Trigger operation not valid');
        }

        if (\strlen($table->getName()) === 0) {
            throw DBALException::invalidTableName($table->getName());
        }

        $this->_name = $operation . '_' . $table->getName();

        $this->operation = $operation;
        $this->table = $table;
        $this->auditTableName = $auditTableName;
        $this->currentStatement = $currentStatement;
        $this->timing = $timing;

        $this->triggerName = $table->getName() . '_audit_' . \strtolower($operation);
        if (\strlen($this->triggerName) > 64) {
            $addition = '_' . \substr(\md5($this->triggerName), 0, 8) . '_audit_' . \strtolower($operation);
            $this->triggerName =  \substr($this->triggerName, 0, 64 - \strlen($addition)) . $addition;
        }
    }

    public function getCurrentStatement(): ?string
    {
        return $this->currentStatement;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getTiming(): ?string
    {
        return $this->timing;
    }

    public function getTriggerName(): string
    {
        return $this->triggerName;
    }

    public function getSql(bool $dropped = false): string
    {
        if ($dropped) {
            return $this->getDropSql();
        }

        return \strtr(self::$createTriggerTemplate, [
            '{{tableName}}' => $this->table->getName(),
            '{{operation}}' => $this->operation,
            '{{triggerName}}' => $this->triggerName,
            '{{timing}}' => $this->timing,
            '{{statement}}' => $this->getStatement(),
        ]);
    }

    public function getStatement(): string
    {
        if ($this->currentStatement) {
            return $this->currentStatement;
        }

        $previous = $this->operation === AuditOperationEnumType::DELETE ? 'OLD' : 'NEW';
        $primaryKeys = \array_map(
            function (string $column) use ($previous) {
                return \strtr(self::$primaryKeysTemplate, [
                    '{{previous}}' => $previous,
                    '{{primaryKey}}' => $column,
                ]);
            },
            $this->table->getPrimaryKeyColumns()
        );
        $primaryKeys = \implode(" AND ", $primaryKeys);

        return \strtr(self::$statementTemplate, [
            '{{tableName}}' => $this->table->getName(),
            '{{operation}}' => $this->operation,
            '{{auditTable}}' => $this->auditTableName,
            '{{primaryKeys}}' => $primaryKeys,
        ]);
    }

    public function getDropSql(): string
    {
        return \strtr('DROP TRIGGER IF EXISTS {{tableName}}_audit_{{operationLower}};', [
            '{{tableName}}' => $this->table->getName(),
            '{{operationLower}}' => \strtolower($this->operation),
        ]);
    }

    public function equals(Trigger $trigger): bool
    {
        return
            \preg_replace('/\s+/', '', $this->getStatement()) === \preg_replace('/\s+/', '', $trigger->getStatement())
            && $this->getTiming() === $trigger->getTiming()
            && $this->getOperation() === $trigger->getOperation()
            // && $this->getTriggerName() === $trigger->getTriggerName();
            && $this->triggerName === $trigger->triggerName;
    }
}
