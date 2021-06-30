<?php
namespace ExEss\Bundle\CmsBundle\Robo\Task\Db\Release\Import;

use Robo\Result;
use ExEss\Bundle\CmsBundle\Robo\Task\Db\Release\AbstractReleaseDb;

abstract class AbstractDbImport extends AbstractReleaseDb
{
    public function run(): Result
    {
        $result = $this->runImport();
        if ($result instanceof Result) {
            return $result;
        }

        return new Result($this, 0, 'successfully imported release sql');
    }

    /**
     * this implements the actual DB exporting
     * @return void|Result
     */
    abstract protected function runImport();

    protected function markSqlFileImported(string $sqlFile): void
    {
        $fullPath = self::RELEASE_DIR . $sqlFile;

        if (\file_exists($fullPath)) {
            \rename($fullPath, $fullPath . '.done_' . \date('Ymd-His'));
        }
    }
}
