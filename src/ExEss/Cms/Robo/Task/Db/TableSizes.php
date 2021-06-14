<?php
namespace ExEss\Cms\Robo\Task\Db;

use Robo\Result;
use Symfony\Component\Console\Output\OutputInterface;

class TableSizes extends AbstractDb
{
    private bool $all;

    private bool $big;

    public function __construct(OutputInterface $output, bool $all, bool $big)
    {
        parent::__construct($output);
        $this->all = $all;
        $this->big = $big;
    }

    public function run(): Result
    {
        $where = '';
        if (!$this->all) {
            if ($this->big) {
                $where = 'AND (data_length + index_length) > 100*1024*1024 ';
            } else {
                $where = 'AND (data_length + index_length) > 10*1024*1024 ';
            }
        }

        $query = \sprintf(
            "SELECT round(((data_length + index_length) / 1024 / 1024), 2) 'MB', table_name 'Table' "
            . "FROM information_schema.TABLES "
            . "WHERE table_schema = '%s' %s"
            . "ORDER BY (data_length + index_length) DESC",
            $this->getDatabase(),
            $where
        );

        return $this->taskExec($this->wrapForCliPipe("echo \"$query\""))->run();
    }
}
