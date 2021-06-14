<?php
namespace ExEss\Cms\FLW_Flows\Event;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvents;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response\Model;
use Symfony\Component\EventDispatcher\EventDispatcher;

class FlowEventDispatcher
{
    private EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return \ExEss\Cms\FLW_Flows\Action\Command|\ExEss\Cms\FLW_Flows\Response
     * @throws \UnexpectedValueException If no Response or Command is returned.
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
        $eventName = FlowEvents::fromDwpEvent($action->getEvent());

        $this->eventDispatcher->dispatch($event, $eventName);

        if ($event->getResponse() === null && $event->getCommand() === null) {
            throw new \UnexpectedValueException(\sprintf(
                'Dispatched flow event %s did not return a response or command, something went wrong.',
                $action->getEvent()
            ));
        }

        return $event->getCommand() ?? $event->getResponse();
    }
}
