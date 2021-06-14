<?php declare(strict_types=1);

namespace ExEss\Cms\Command\ClearConfig;

use ExEss\Cms\Command\AbstractAdminCommand;
use ExEss\Cms\Repository\ClearConfigRepository;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

abstract class AbstractClearConfigCommand extends AbstractAdminCommand
{
    private const DISPLAY_NO_RECORDS = 5;

    protected ClearConfigRepository $clearConfigRepository;

    public function __construct(ClearConfigRepository $clearConfigRepository)
    {
        parent::__construct();

        $this->clearConfigRepository = $clearConfigRepository;
    }

    protected function displayRecords(array $records): void
    {
        $totalRecords = 0;

        $outputTable = new Table($this->io);
        $outputTable->setHeaders(['Table', 'Count', 'Records']);
        $outputTable->setStyle('box-double');
        foreach ($records as $table => $ids) {
            $displayTable = $table;
            $displayCount = \count($ids);
            $totalRecords += $displayCount;

            if ($displayCount === 0) {
                $outputTable->addRow([$displayTable, $displayCount, ""]);
            }

            foreach (\array_slice($ids, 0, self::DISPLAY_NO_RECORDS) as $id => $name) {
                $outputTable->addRow([$displayTable, $displayCount, $id . " -> " . $name]);
                $displayCount = "";
                $displayTable = "";
            }

            if (\count($ids) > self::DISPLAY_NO_RECORDS) {
                $outputTable->addRow(["", "", "..."]);
            }

            $outputTable->addRow(new TableSeparator());
        }

        $outputTable->addRow(["TOTAL", $totalRecords, ""]);

        $outputTable->render();
    }

    protected function deleteRecords(array $records): void
    {
        $outputTable = new Table($this->io);
        $outputTable->setHeaders(['Table', 'Records to delete', 'Processed records', 'Status']);
        $outputTable->setStyle('box-double');

        $totalRecords = 0;
        $totalRelations = 0;
        foreach ($records as $table => $ids) {
            $deletedRecords = $this->clearConfigRepository->deleteRecords($table, \array_keys($ids));
            $totalRecords += $deletedRecords;
            $outputTable->addRow([
                $table,
                \count($ids),
                $deletedRecords,
                $deletedRecords === \count($ids) ? 'OK' : 'ERROR'
            ]);
            $outputTable->addRow(new TableSeparator());

            $relationTables = $this->clearConfigRepository->findRelationTables($table);
            foreach ($relationTables as $relationTable) {
                $deletedRecords = $this->clearConfigRepository->deleteRelationRecords(
                    $table,
                    $relationTable['rel_table'],
                    $relationTable['rel_field']
                );

                if ($deletedRecords > 0) {
                    $totalRelations += $deletedRecords;
                    $outputTable->addRow([
                        $relationTable['rel_table'],
                        "-",
                        $deletedRecords,
                        'OK'
                    ]);
                    $outputTable->addRow(new TableSeparator());
                }
            }
        }

        $outputTable->addRow(["TOTAL records", $totalRecords, ""]);
        $outputTable->addRow(new TableSeparator());
        $outputTable->addRow(["TOTAL relations", $totalRelations, ""]);

        $outputTable->render();
    }
}
