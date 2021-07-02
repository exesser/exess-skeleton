<?php
namespace ExEss\Cms\Robo\Task;

use ExEss\Bundle\CmsBundle\Helper\DataCleaner;

trait CrudAware
{
    protected static string $recordsPath = 'vendor/exesser/exess/src/Bundle/cms-bundle/CRUD/Config/records';

    protected function yieldCrudRecords(): \Generator
    {
        if (!\is_dir($path = self::$recordsPath)) {
            throw new \DomainException("Path $path doesn't exist!");
        }
        foreach (\glob("$path/*.json") as $file) {
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
