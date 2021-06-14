<?php

namespace ExEss\Cms\CRUD\Config;

use Robo\Result;
use ExEss\Cms\Robo\Task\CrudAware;
use ExEss\Cms\Robo\Task\Db\AbstractDb;

class ConfigurationTask extends AbstractDb
{
    use CrudAware;

    private const INSERT_SQL_TEMPLATE = 'INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s; ';

    public function run(): Result
    {
        $pdo = $this->getPdo();
        $pdo->beginTransaction();
        foreach ($this->generateSql() as $table => $queries) {
            foreach ($queries as $query) {
                $pdo->exec($query);
            }
        }
        try {
            $pdo->commit();
        } catch (\PDOException $e) {
            $pdo->rollBack();
            $this->io()->warning(
                "CRUD configuration was not imported to " . $this->getDatabase() . ' failed due to: ' . $e->getMessage()
            );
            return new Result($this, Result::EXITCODE_ERROR);
        }

        return new Result($this, Result::EXITCODE_OK);
    }

    private function generateSql(): array
    {
        $sql = [];

        foreach ($this->yieldCrudRecords() as $table => $record) {
            $record = \array_map(
                function ($fieldValue) {
                    if ($fieldValue === false) {
                        return 0;
                    }

                    if ($fieldValue === true) {
                        return 1;
                    }

                    if (\is_string($fieldValue)) {
                        return "'" . $fieldValue . "'";
                    }

                    if ($fieldValue === null) {
                        return 'null';
                    }

                    return $fieldValue;
                },
                $record
            );

            $update = \array_map(
                function ($value, $key): ?string {
                    return $key !== 'id' ? \sprintf('%s=%s', $key, $value) : null;
                },
                $record,
                \array_keys($record)
            );

            $update = \implode(',', \array_filter($update));

            $sql[$table] = $sql[$table] ?? [];
            $sql[$table][] = \sprintf(
                self::INSERT_SQL_TEMPLATE,
                $table,
                \implode(',', \array_keys($record)),
                \implode(',', \array_values($record)),
                $update
            );
        }

        return $sql;
    }
}
