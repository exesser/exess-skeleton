<?php
namespace ExEss\Cms\Robo\Task;

use ExEss\Cms\Helper\DataCleaner;

trait CrudAware
{
    protected function yieldCrudRecords(): \Generator
    {
        foreach (\glob('src/ExEss/Cms/CRUD/Config/records' . \DIRECTORY_SEPARATOR . '*.json') as $file) {
            $tables = DataCleaner::jsonDecode(\file_get_contents($file));
            foreach ($tables as $table => $records) {
                foreach ($records as $record) {
                    yield $table => $record;
                }
            }
        }
    }

    protected function getCrudIds(): array
    {
        $ids = [];
        foreach ($this->yieldCrudRecords() as $table => $record) {
            $ids[] = $record['id'];
        }

        return $ids;
    }
}
