<?php
namespace ExEss\Cms\Robo\Task\Db;

use Robo\Result;
use Symfony\Component\Console\Output\OutputInterface;

class DumpToDatabase extends AbstractDb
{
    private string $options;

    private string $toDatabase;

    private string $pipeThrough;

    private string $tables;

    public function __construct(
        OutputInterface $output,
        string $options,
        array $tables,
        ?string $fromDatabase = null,
        string $toDatabase,
        string $pipeThrough = ''
    ) {
        parent::__construct($output, $fromDatabase);
        $this->tables = \implode(' ', $tables);
        $this->output = $output;
        $this->options = $options;
        $this->toDatabase = $toDatabase;
        $this->pipeThrough = $pipeThrough;
    }

    public function run(): Result
    {
        return $this->taskExec(
            $this->wrapForCliPipe(
                $this->wrapForCliDump(
                    $this->options,
                    $this->getDatabase()
                )
                . " $this->tables "
                . ($this->pipeThrough ? ' | ' . $this->pipeThrough : ''),
                true,
                $this->toDatabase
            )
        )->run();
    }
}
