<?php
namespace App\Robo\Task\Db\Release;

use PDO;
use PDOStatement;
use Robo\Result;
use App\Robo\Task\Db\AbstractDb;
use App\Robo\Task\Db\DumpToFile;

abstract class AbstractReleaseDb extends AbstractDb
{
    protected const RELEASE_DIR = __DIR__ . '/../../../../../../dev/database/releases';

    /**
     * Activate this to export records and audits.
     */
    protected bool $forceExportAll = false;

    /**
     * Existing tables on current database.
     */
    protected array $tablesInDb = [];

    protected string $subPath = '';

    public function setForceExportAll(bool $forceExportAll = true): self
    {
        $this->forceExportAll = $forceExportAll;

        return $this;
    }

    protected function appendToFile(string $table, string $where, string $sqlFile, bool $insertOnly = true): void
    {
        $this->dump([$table], $where, $sqlFile, $insertOnly);

        if (!$this->forceExportAll) {
            return;
        }

        $audTables = $this->getAuditTable([$table]);
        if (empty($audTables)) {
            return;
        }

        $this->dump($audTables, $where, $this->getAuditSqlFile($sqlFile), $insertOnly);
    }

    protected function dump(array $tables, string $where, string $sqlFile, bool $insertOnly): Result
    {
        $pipeThrough = '';
        if (!$insertOnly) {
            foreach ($tables as $table) {
                $pipeThrough .= "sed 's/";
                $pipeThrough .= "\/\*!40000 ALTER TABLE `$table` DISABLE KEYS \*\/;";
                $pipeThrough .= "/";
                $pipeThrough .= "\/\*!40000 ALTER TABLE `$table` DISABLE KEYS \*\/;";
                $pipeThrough .= "\\nDELETE FROM `$table`;\\n";
                $pipeThrough .= "/g' | ";
            }
        }
        $pipeThrough .= "sed \"s/),('/),\\n('/g\" | sed \"s/VALUES ('/VALUES \\n('/g\"";

        return (
            new DumpToFile(
                $this->output(),
                "--skip-dump-date" . ($insertOnly ? ' --replace' : ''),
                $tables,
                (!empty($where) ? $where : '1=1') . ' ORDER BY id ASC',
                self::RELEASE_DIR . $sqlFile,
                $this->getDatabase(),
                $pipeThrough
            )
        )->run();
    }

    protected function getAuditTable(array $tables): array
    {
        $audTables = \array_map(
            function ($table) {
                return $table . '_aud';
            },
            $tables
        );

        return \array_intersect($audTables, $this->getTablesFromDb());
    }

    protected function getAuditSqlFile(string $sqlFile): string
    {
        return \str_replace('.sql', '_aud.sql', $sqlFile);
    }

    private function getTablesFromDb(): array
    {
        if (empty($this->tablesInDb)) {
            $dbh = $this->getPdo();
            $query = \sprintf(
                "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s'",
                $this->getDatabase()
            );

            $this->tablesInDb = \array_column($dbh->query($query)->fetchAll(), 'TABLE_NAME');
        }

        return $this->tablesInDb;
    }

    protected function appendResultToFile(PDOStatement $result, string $sqlFile, array $sqlFileForTable = []): array
    {
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        $tables = [];

        foreach ($rows as $row) {
            foreach ($row as $table => $id) {
                if ($id) {
                    $tables[$table][$id] = $id;
                }
            }
        }

        foreach ($tables as $table => $ids) {
            foreach (\array_chunk($ids, 1000) as $idsSet) {
                $this->appendToFile(
                    $table,
                    \sprintf("id in ('%s')", \implode("', '", $idsSet)),
                    $sqlFileForTable[$table] ?? $sqlFile
                );
            }
        }

        return $tables;
    }
}
