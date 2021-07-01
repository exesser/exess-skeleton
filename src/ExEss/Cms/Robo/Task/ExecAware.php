<?php

namespace ExEss\Cms\Robo\Task;

use Robo\Collection\CollectionBuilder;
use Robo\Robo;
use Robo\Task\Base\loadTasks;
use Robo\TaskAccessor;

trait ExecAware
{
    use TaskAccessor;
    use loadTasks;

    /**
     * Scaffold the collection builder
     */
    public function collectionBuilder(): CollectionBuilder
    {
        return Robo::createDefaultContainer()->get('collectionBuilder', [new \Robo\Tasks]);
    }
}
