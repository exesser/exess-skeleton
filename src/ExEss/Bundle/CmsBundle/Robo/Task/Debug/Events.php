<?php
namespace ExEss\Bundle\CmsBundle\Robo\Task\Debug;

use Robo\Result;
use Robo\Task\BaseTask;
use ExEss\Bundle\CmsBundle\Robo\Task\TaskHelper;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Events extends BaseTask
{
    use TaskHelper;
    private SymfonyStyle $io;

    private string $filter;

    public function __construct(SymfonyStyle $io, ?string $filter = null)
    {
        $this->io = $io;
        $this->filter = \strtolower($filter);
    }

    public function run(): Result
    {
        $dispatcher = $this->getContainer()->get(EventDispatcher::class);
        $eventListeners = $dispatcher->getListeners();

        $rows = [];
        foreach ($eventListeners as $event => $listeners) {
            foreach ($listeners as $listener) {
                $class = \get_class($listener[0]);
                $method = $listener[1];
                if ($this->filter
                    && \stripos($event, $this->filter) === false
                    && \stripos($class, $this->filter) === false
                    && \stripos($method, $this->filter) === false
                ) {
                    continue;
                }
                $rows[] = [$event, $dispatcher->getListenerPriority($event, $listener), $class, $method];
            }
        }
        $this->io->note('higher priority means earlier execution');
        $this->io->table(['event', 'prio', 'class', 'method'], $rows);

        return new Result($this, Result::EXITCODE_OK);
    }
}
