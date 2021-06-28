<?php
namespace ExEss\Cms\Component\Flow\Event;

use ExEss\Cms\Component\Flow\Action\BackendCommandExecutor;
use ExEss\Cms\Component\Flow\Request\FlowAction;
use ExEss\Cms\Component\Flow\Response\Model;
use Symfony\Component\EventDispatcher\EventDispatcher;

class FlowEventDispatcher
{
    private EventDispatcher $eventDispatcher;

    private BackendCommandExecutor $commandExecutor;

    public function __construct(EventDispatcher $eventDispatcher, BackendCommandExecutor $commandExecutor)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->commandExecutor = $commandExecutor;
    }

    /**
     * @return \ExEss\Cms\Component\Flow\Action\Command|\ExEss\Cms\Component\Flow\Response
     */
    public function dispatch(
        string $flowKey,
        FlowAction $action,
        Model $model,
        ?Model $parenModel = null,
        array $params = [],
        ?string $recordType = null,
        ?string $guidanceAction = null,
        ?array $route = null
    ) {
        $event = new FlowEvent(
            $flowKey,
            $action,
            $model,
            $parenModel,
            $params,
            $recordType,
            $guidanceAction,
            $route
        );

        $this->eventDispatcher->dispatch($event, FlowEvents::fromDwpEvent($action->getEvent()));

        if ($command = $event->getCommand()) {
            $this->commandExecutor->execute($command, $command->getArguments()->recordIds, $model);
            return $command;
        }

        return $event->getResponse();
    }
}
