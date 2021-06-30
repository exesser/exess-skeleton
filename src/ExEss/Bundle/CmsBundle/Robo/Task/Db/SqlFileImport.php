<?php
namespace ExEss\Bundle\CmsBundle\Robo\Task\Db;

use Robo\Result;
use Symfony\Component\Console\Output\OutputInterface;

class SqlFileImport extends AbstractDb
{
    private array $files;

    public function __construct(OutputInterface $output, array $files, ?string $databaseName = null)
    {
        parent::__construct($output, $databaseName);
        $this->files = $files;
    }

    public function run(): Result
    {
        foreach ($this->files as $filePath) {
            if (!\file_exists($filePath)) {
                $this->output()->error($filePath . ' not found');
                return new Result($this, Result::EXITCODE_ERROR, "$filePath NOT imported, file not found");
            }

            $result = $this->taskExec(
                $this->wrapCli('mysql', true, $this->getDatabase()) . " < $filePath"
            )->run();

            if (!$result->wasSuccessful()) {
                return new Result($this, Result::EXITCODE_ERROR, "$filePath NOT imported, error occurred");
            }

            $this->output()->writeln("$filePath imported into database " . $this->getDatabase());
        }

        return new Result($this, Result::EXITCODE_OK);
    }
}
