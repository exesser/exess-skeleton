<?php
namespace ExEss\Cms\Robo\Task\Db\Release\Export;

use ExEss\Bundle\CmsBundle\Dictionary\Format;
use ExEss\Bundle\CmsBundle\Exception\NotFoundException;
use ExEss\Bundle\CmsBundle\Factory\Mapping\ClassMetadata;
use PDO;
use Robo\Result;
use ExEss\Cms\Robo\Task\CrudAware;
use ExEss\Cms\Robo\Task\Db\Release\AbstractReleaseDb;

abstract class AbstractDbExport extends AbstractReleaseDb
{
    use CrudAware;

    private const ON_UPDATE = 'UPDATE';
    private const ON_INSERT = 'INSERT';
    private const ON_DELETE = 'DELETE';

    private const AUDIT_TIMESTAMP = 'audit_timestamp';
    private const AUDIT_OPERATION = 'audit_operation';
    private const AUDIT_PROCESSED = 'audit_processed';
    private const AUDIT_ID = 'id';

    private const RELEASE_NOTE_PREFIX = 'RELEASE_NOTES_';
    private const RELEASE_NOTE_TIMESTAMP_FORMAT = 'YmdHis';

    public const DATE_MODIFIED = 'date_modified';
    public const DATE_ENTERED = 'date_entered';
    public const MODIFIED_USER_ID = 'modified_user_id';
    public const CREATED_BY = 'created_by';

    public const AUDIT_SYSTEM_FIELDS = [
        self::AUDIT_TIMESTAMP,
        self::AUDIT_OPERATION,
        self::AUDIT_PROCESSED,
        self::AUDIT_ID,
        self::DATE_MODIFIED,
        self::DATE_ENTERED,
        self::MODIFIED_USER_ID,
        self::CREATED_BY,
    ];

    public function run(): Result
    {
        $this->cleanUp();
        $result = $this->runExport();
        if ($result instanceof Result) {
            return $result;
        }

        return new Result($this, 0, 'successfully created release sql');
    }

    /**
     * prepares the release folder to accept the new sql files
     */
    protected function cleanUp(): void
    {
        if (!\file_exists(self::RELEASE_DIR . $this->subPath)) {
            \mkdir(self::RELEASE_DIR . $this->subPath);
            return;
        }

        // remove all existing files in the folder
        \array_map('unlink', \glob(self::RELEASE_DIR . $this->subPath . '/*'));
    }

    protected function getFatEntityManager(): Manager
    {
        return $this->getContainer()->get(Manager::class);
    }

    /**
     * this implements the actual DB exporting
     * @return void|Result
     */
    abstract protected function runExport();

    protected function dumpBeanTables(array $fatEntities, string $file): array
    {
        $tables = \array_map(
            function (ClassMetadata $metadata): string {
                return $metadata->getTableName();
            },
            $fatEntities
        );
        $tables = \array_unique($tables);
        $this->dumpToFile($tables, $file);
        $auditTables = $this->getAuditTable($tables);

        $auditedTables = [];
        $error = [];
        /** @var ClassMetadata $metadata */
        foreach ($fatEntities as $metadata) {
            $table = $metadata->getTableName();
            if (!\in_array("{$table}_aud", $auditTables)) {
                $error[] = $table;
            } else {
                $auditedTables[$table] = [
                    'bean' => $metadata->getModuleName(),
                    'table' => $table,
                    'type' => 'table',
                ];
            }
        }

        if (\count($error)) {
            throw new \DomainException(
                'Missing audit tables: ' . \implode(', ', $error)
            );
        }

        return $auditedTables;
    }

    protected function dumpToFile(
        array $tables,
        string $sqlFile
    ): void {
        $this->dump($tables, '', $sqlFile, false);

        if (!$this->forceExportAll) {
            return;
        }

        $audTables = $this->getAuditTable($tables);
        if (empty($audTables)) {
            return;
        }

        $this->dump($audTables, '', $this->getAuditSqlFile($sqlFile), false);
    }

    protected function dumpRelationshipsTables(array $fatEntities, string $file): array
    {
        // @todo rewrite based on entities
        // $allClassNames = \array_map(
        //     function (ClassMetadata $metadata): string {
        //         return $metadata->getName();
        //     },
        //     $fatEntities
        // );
        //
        // $tables = [];
        // $auditedRelationTables = [];
        // /** @var ClassMetadata $metadata */
        // foreach ($fatEntities as $metadata) {
        //     $fatEntity = $this->getFatEntityManager()->newFatEntitySafe($metadata->getName());
        //     foreach ($metadata->getAssociationNames() as $fieldName) {
        //         /** @var \AbstractRelationship $relationship */
        //         $relationship = $fatEntity->loadRelationship($fieldName)->getRelationshipObject();
        //         $table = $relationship->getRelationshipTable();
        //         if (!$table || isset($tables[$table])) {
        //             continue;
        //         }
        //
        //         $otherBean = $relationship->getRHSModule() === $metadata->getName()
        //             ? $relationship->getLHSModule()
        //             : $relationship->getRHSModule();
        //
        //         if (!\in_array($otherBean, $allClassNames, true)) {
        //             continue; //the other fat entity is not config
        //         }
        //
        //         $condition = "%s IN (SELECT %s from %s)";
        //
        //         $tables[$table][] = \sprintf(
        //             $condition,
        //             $relationship->getLHSJoinKey(),
        //             $relationship->getLHSKey(),
        //             $relationship->getLHSTable()
        //         );
        //
        //         $tables[$table][] = \sprintf(
        //             $condition,
        //             $relationship->getRHSJoinKey(),
        //             $relationship->getRHSKey(),
        //             $relationship->getRHSTable()
        //         );
        //
        //         if (!\array_key_exists($table, $auditedRelationTables)) {
        //             $auditedRelationTables[$table] = [
        //                 'lhsBean' => $relationship->getLHSModule(),
        //                 'rhsBean' => $relationship->getRHSModule(),
        //                 'lhsKey' => $relationship->getLHSJoinKey(),
        //                 'rhsKey' => $relationship->getRHSJoinKey(),
        //                 'type' => 'relation',
        //                 'table' => $table,
        //             ];
        //         }
        //     }
        // }
        //
        // foreach ($tables as $table => $conditions) {
        //     $this->appendToFile(
        //         $table,
        //         \implode(' AND ', $conditions),
        //         $file,
        //         false
        //     );
        // }
        //
        // return $auditedRelationTables;
    }

    protected function dumpSecurityGroupTables(array $fatEntities, string $sqlFile): void
    {
        // @todo fix this
        return;

        //SecurityGroup is using the module_dir for module in securitygroups_records table.
        $modules = \array_map(
            function (ClassMetadata $metadata): string {
                return $metadata->getModuleName();
            },
            $fatEntities
        );

        $query = \sprintf(
            "module in ('%s')",
            \implode("', '", $modules)
        );

        $this->appendToFile(
            'securitygroups_records',
            $query,
            $sqlFile
        );
    }

    protected function generateReleaseNotes(array $scope): void
    {
        if ($this->forceExportAll) {
            $this->io()->writeln('No release notes being generated for export all');
            return;
        }

        $this->io()->writeln('Generating releasenotes from audit tables');

        $fromDate = $this->getPreviousReleaseNoteDate();
        $toDate = new \DateTimeImmutable();

        $changes = [];
        foreach ($scope as $table) {
            switch ($table['type']) {
                case 'table':
                    $changes = \array_merge($changes, $this->getTableAudits($table, $fromDate, $toDate));
                    break;
                case 'relation':
                    $changes = \array_merge($changes, $this->getRelationAudits($table, $fromDate, $toDate));
                    break;
            }
        }

        if (!empty($changes)) {
            $fileName = self::RELEASE_DIR .
                '/' .
                self::RELEASE_NOTE_PREFIX .
                $toDate->format(self::RELEASE_NOTE_TIMESTAMP_FORMAT) .
                '.txt';

            \sort($changes);

            $written = \file_put_contents(
                $fileName,
                \implode("\r\n", $changes)
            );

            if (!$written) {
                throw new \DomainException('Could not write changes to file: ' . $fileName);
            }

            $this->io()->writeln(
                \sprintf(
                    'Release note file %s was succesfully generated',
                    $fileName
                )
            );
        }
    }

    private function getTableAudits(array $table, \DateTimeImmutable $fromDate, \DateTimeImmutable $toDate): array
    {
        $changes = [];
        $query = \sprintf(
            "SELECT * FROM %s_aud 
                WHERE audit_timestamp > '%s'
                AND audit_timestamp <= '%s'
                ORDER BY audit_timestamp asc
                ",
            $table['table'],
            $fromDate->format(Format::DB_DATETIME_FORMAT),
            $toDate->format(Format::DB_DATETIME_FORMAT)
        );

        $dbh = $this->getPdo();
        $sql = $dbh->query($query);
        if ($sql === false) {
            throw new \DomainException('Error while executing audit query for table ' . $table['table']);
        }

        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
        $crudIds = $this->getCrudIds();
        foreach ($rows as $row) {
            if (\in_array($row['id'], $crudIds, true)) {
                continue;
            }
            try {
                $fatEntity = $this
                    ->getFatEntityManager()
                    ->getFatEntitySafe($table['bean'], $row[self::AUDIT_ID], [], false);

                switch ($row[self::AUDIT_OPERATION]) {
                    case self::ON_INSERT:
                        $changes[] = \sprintf(
                            '%s - %s \'%s\' (%s) was inserted',
                            $row[self::AUDIT_TIMESTAMP],
                            $table['bean'],
                            $fatEntity->getCrudListC1R1(),
                            $fatEntity->id
                        );
                        break;
                    case self::ON_UPDATE:
                        $recordChanges = $this->getChangedFields($table, $row);
                        if (!empty($recordChanges)) {
                            $changes[] = \sprintf(
                                '%s - %s \'%s\' (%s) was updated',
                                $row[self::AUDIT_TIMESTAMP],
                                $table['bean'],
                                $fatEntity->getCrudListC1R1(),
                                $fatEntity->id
                            ) . (': \'' . \implode('\', \'', $recordChanges) . '\' changed');
                        }
                        break;
                    case self::ON_DELETE:
                        throw new NotFoundException(
                            'This is just impossible, what is happening? ' .
                            'You\'re not doing an export on dev11 right?'
                        );
                    default:
                        throw new \DomainException(
                            \sprintf(
                                '%s - %s %s: Audit operation %s is not supported',
                                $row[self::AUDIT_TIMESTAMP],
                                $table['table'],
                                $row[self::AUDIT_ID],
                                $row[self::AUDIT_OPERATION]
                            )
                        );
                }
            } catch (NotFoundException $e) {
                $changes[] = \sprintf(
                    '%s - %s (%s) was deleted',
                    $row[self::AUDIT_TIMESTAMP],
                    $table['bean'],
                    $row[self::AUDIT_ID]
                );
            }
        }

        return $changes;
    }

    private function getRelationAudits(array $table, \DateTimeImmutable $fromDate, \DateTimeImmutable $toDate): array
    {
        $changes = [];
        $query = \sprintf(
            "SELECT id, audit_operation, audit_timestamp, %s, %s FROM %s_aud 
                            WHERE audit_timestamp > '%s'
                            AND audit_timestamp <= '%s'
                            ORDER BY audit_timestamp asc
                            ",
            $table['lhsKey'],
            $table['rhsKey'],
            $table['table'],
            $fromDate->format(Format::DB_DATETIME_FORMAT),
            $toDate->format(Format::DB_DATETIME_FORMAT)
        );

        $dbh = $this->getPdo();
        $sql = $dbh->query($query);
        if ($sql === false) {
            throw new \DomainException('Error while executing audit query for table ' . $table['table']);
        }
        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
        $crudIds = $this->getCrudIds();
        foreach ($rows as $row) {
            if (\in_array($row['id'], $crudIds, true)) {
                continue;
            }
            try {
                $lhsFatEntity = $this->getFatEntityManager()->getFatEntitySafe(
                    $table['lhsBean'],
                    $row[$table['lhsKey']]
                );
                $rhsFatEntity = $this->getFatEntityManager()->getFatEntitySafe(
                    $table['rhsBean'],
                    $row[$table['rhsKey']]
                );
                switch ($row[self::AUDIT_OPERATION]) {
                    case self::ON_INSERT:
                        $changes[] = \sprintf(
                            '%s - Relation (%s) was added between %s \'%s\' (%s) and %s \'%s\' (%s)',
                            $row[self::AUDIT_TIMESTAMP],
                            $row[self::AUDIT_ID],
                            $table['lhsBean'],
                            $lhsFatEntity->getCrudListC1R1(),
                            $lhsFatEntity->id,
                            $table['rhsBean'],
                            $rhsFatEntity->getCrudListC1R1(),
                            $rhsFatEntity->id
                        );
                        break;
                    case self::ON_UPDATE:
                        $change = \sprintf(
                            '%s - Relation (%s) was updated between %s \'%s\' (%s) and %s \'%s\' (%s)',
                            $row[self::AUDIT_TIMESTAMP],
                            $row[self::AUDIT_ID],
                            $table['lhsBean'],
                            $lhsFatEntity->getCrudListC1R1(),
                            $lhsFatEntity->id,
                            $table['rhsBean'],
                            $rhsFatEntity->getCrudListC1R1(),
                            $rhsFatEntity->id
                        );
                        $recordChanges = $this->getChangedFields($table, $row);
                        if (!empty($recordChanges)) {
                            $changes[] = $change . ': \'' . \implode('\', \'', $recordChanges) . '\' changed';
                        }
                        break;
                    case self::ON_DELETE:
                        $changes[] = \sprintf(
                            '%s - Relation (%s) was deleted between %s \'%s\' (%s) and %s \'%s\' (%s)',
                            $row[self::AUDIT_TIMESTAMP],
                            $row[self::AUDIT_ID],
                            $table['lhsBean'],
                            $lhsFatEntity->getCrudListC1R1(),
                            $lhsFatEntity->id,
                            $table['rhsBean'],
                            $rhsFatEntity->getCrudListC1R1(),
                            $rhsFatEntity->id
                        );
                        break;
                    default:
                        throw new \DomainException(
                            \sprintf(
                                '%s - %s %s: Audit operation %s is not supported',
                                $row[self::AUDIT_TIMESTAMP],
                                $table['table'],
                                $table[self::AUDIT_ID],
                                $row[self::AUDIT_OPERATION]
                            )
                        );
                }
            } catch (NotFoundException $e) {
                $changes[] = \sprintf(
                    '%s - Beans in relation between %s (%s) and %s (%s) were possibly deleted',
                    $row[self::AUDIT_TIMESTAMP],
                    $table['lhsBean'],
                    $row[$table['lhsKey']],
                    $table['rhsBean'],
                    $row[$table['rhsKey']]
                );
            }
        }

        return $changes;
    }

    private function getPreviousReleaseNoteDate(): \DateTimeImmutable
    {
        $releaseNotes = \glob(self::RELEASE_DIR . '/*.txt');
        if (empty($releaseNotes)) {
            throw new \DomainException('No previous release note found');
        } else {
            \preg_match('/([0-9]{14})\.txt+$/', \max($releaseNotes), $matches);
            $timeString = $matches[1] ?? '19000101000000';
        }

        return \DateTimeImmutable::createFromFormat(self::RELEASE_NOTE_TIMESTAMP_FORMAT, $timeString);
    }

    private function getChangedFields(array $table, array $audit): array
    {
        $prevAudit = $this->getPreviousAuditRecord($table, $audit);

        if (empty($audit) || empty($prevAudit)) {
            return [];
        }

        $changes = [];
        foreach ($audit as $field => $value) {
            if (\in_array($field, self::AUDIT_SYSTEM_FIELDS)) {
                continue;
            }

            if ($prevAudit[$field] !== $value) {
                $changes[] = $field;
            }
        }
        return $changes;
    }

    private function getPreviousAuditRecord(array $table, array $row): array
    {
        $query = \sprintf(
            "SELECT * FROM %s_aud WHERE id = '%s' AND audit_timestamp < '%s' ORDER BY audit_timestamp desc LIMIT 1 ",
            $table['table'],
            $row[self::AUDIT_ID],
            $row[self::AUDIT_TIMESTAMP]
        );

        $dbh = $this->getPdo();
        $sql = $dbh->query($query);

        if ($sql === false) {
            throw new \DomainException('Error while executing prevaudit query for table ' . $table['table']);
        }
        $result = $sql->fetch(PDO::FETCH_ASSOC);

        return $result ? $result : [];
    }
}
