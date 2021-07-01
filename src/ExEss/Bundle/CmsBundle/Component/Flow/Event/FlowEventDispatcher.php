<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Event;

use ExEss\Bundle\CmsBundle\Component\Flow\Action\BackendCommandExecutor;
use ExEss\Bundle\CmsBundle\Component\Flow\Request\FlowAction;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FlowEventDispatcher
{
    private EventDispatcherInterface $eventDispatcher;

    private BackendCommandExecutor $commandExecutor;

    public function __construct(EventDispatcherInterface $eventDispatcher, BackendCommandExecutor $commandExecutor)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->commandExecutor = $commandExecutor;
    }

    /**
     * @return \ExEss\Bundle\CmsBundle\Component\Flow\Action\Command|\ExEss\Bundle\CmsBundle\Component\Flow\Response
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
