<?php
namespace ExEss\Bundle\CmsBundle\Robo\Task\Db;

use PDO;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\BuilderAwareInterface;
use Robo\Result;
use Robo\Robo;
use Robo\Task\Base\loadTasks;
use Robo\Task\BaseTask;
use Robo\Task\File\loadTasks as fileTasks;
use Robo\TaskAccessor;
use ExEss\Bundle\CmsBundle\Robo\Task\DatabaseAware;
use ExEss\Bundle\CmsBundle\Robo\Task\TaskHelper;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractDb extends BaseTask implements BuilderAwareInterface
{
    use TaskAccessor;
    use TaskHelper;
    use loadTasks;
    use fileTasks;
    use DatabaseAware;

    private string $database;

    public function __construct(
        OutputInterface $output,
        ?string $database = null
    ) {
        $this->database = $this->getDatabaseConfig($database)['db_name'];

        $this->setOutput($output);

        Robo::logger()->setOutputStream($output);

        // if any of the execs fails, stop execution immediately
        Result::$stopOnFail = true;

        // this might take a bit of memory to complete
        \ini_set('memory_limit', '-1');
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    protected function getPdo(): PDO
    {
        return $this->getPdoConnection($this->getDatabase());
    }

    /**
     * Scaffold the collection builder
     */
    public function collectionBuilder(): CollectionBuilder
    {
        return Robo::createDefaultContainer()->get('collectionBuilder', [new \Robo\Tasks]);
    }
}
