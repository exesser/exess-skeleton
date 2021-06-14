<?php
namespace ExEss\Cms\Robo\Task\Db;

use Robo\Result;
use Symfony\Component\Console\Output\OutputInterface;

class DumpToFile extends AbstractDb
{
    private string $options;

    private string $toFile;

    private string $tables;

    private string $where;

    private string $pipeThrough;

    public function __construct(
        OutputInterface $output,
        string $options,
        array $tables,
        string $where,
        string $toFile,
        ?string $database = null,
        string $pipeThrough = ''
    ) {
        parent::__construct($output, $database);
        $this->options = $options;
        $this->toFile = $toFile;
        $this->tables = \implode(' ', $tables);
        $this->where = $where;
        $this->pipeThrough = $pipeThrough;
    }

    public function run(): Result
    {
        return $this->taskExec(
            $this->wrapForCliDump(
                " --single-transaction --no-create-info --complete-insert --where=\"$this->where\" $this->options",
                $this->getDatabase()
            )
            . " $this->tables "
            . ($this->pipeThrough ? ' | ' . $this->pipeThrough : '')
            . " >> $this->toFile"
        )->run();
    }
}
