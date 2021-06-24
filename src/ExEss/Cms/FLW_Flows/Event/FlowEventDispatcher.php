<?php
namespace ExEss\Cms\FLW_Flows\Event;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvents;
use ExEss\Cms\FLW_Flows\Action\BackendCommandExecutor;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response\Model;
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
     * @return \ExEss\Cms\FLW_Flows\Action\Command|\ExEss\Cms\FLW_Flows\Response
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
