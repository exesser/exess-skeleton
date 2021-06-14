<?php
namespace ExEss\Cms\Robo\Task;

trait CrudAware
{
    protected function yieldCrudRecords(): \Generator
    {
        foreach (\glob('src/ExEss/Cms/CRUD/Config/records' . \DIRECTORY_SEPARATOR . '*.json') as $file) {
            $tables = \json_decode(\file_get_contents($file), true, 512, \JSON_THROW_ON_ERROR);
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
