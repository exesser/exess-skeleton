<?php

namespace ExEss\Cms\Api\V8_Custom\Events;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\FLW_Flows\Action\Command;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use Symfony\Contracts\EventDispatcher\Event;

class FlowEvent extends Event
{
    private string $flowKey;

    private FlowAction $action;

    private Response\Model $model;

    private array $params;

    private ?string $recordType;

    private ?string $guidanceAction;

    private ?Response $response = null;

    private ?Command $command = null;

    private ?Flow $flow = null;

    private ?string $recordId = null;

    private ?Response\NextStep $nextStep = null;

    private ?object $baseEntity = null;

    private ?Response\Model $parentModel;

    private ?array $route;

    public function __construct(
        string $flowKey,
        FlowAction $action,
        Response\Model $model,
        ?Response\Model $parentModel = null,
        array $params = [],
        ?string $recordType = null,
        ?string $guidanceAction = null,
        ?array $route = null
    ) {
        $this->flowKey = $flowKey;
        $this->action = $action;
        $this->model = $model;
        $this->parentModel = $parentModel;
        $this->params = $params;
        $this->recordType = $recordType;
        $this->guidanceAction = $guidanceAction;
        $this->route = $route;
    }

    public function getFlowKey(): string
    {
        return $this->flow ? $this->flow->getKey() : $this->flowKey;
    }

    public function getAction(): FlowAction
    {
        return $this->action;
    }

    public function getModel(): Response\Model
    {
        return $this->model;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getRecordType(): ?string
    {
        return $this->recordType;
    }

    public function getGuidanceAction(): ?string
    {
        return $this->guidanceAction;
    }

    public function getResponse(): Response
    {
        // lazy init this
        if (!$this->response) {
            $this->response = new Response();
        }

        return $this->response;
    }

    public function getCommand(): ?Command
    {
        return $this->command;
    }

    public function getFlow(): Flow
    {
        return $this->flow;
    }

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getNextStep(): ?Response\NextStep
    {
        return $this->nextStep;
    }

    public function getBaseEntity(): ?object
    {
        return $this->baseEntity;
    }

    public function getRoute(): ?array
    {
        return $this->route;
    }

    public function getParentModel(): ?Response\Model
    {
        return $this->parentModel;
    }

    public function setFlow(Flow $flow): void
    {
        $this->flow = $flow;
    }

    public function setRecordType(string $recordType): void
    {
        $this->recordType = $recordType;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function setCommand(?Command $command): void
    {
        $this->command = $command;
    }

    public function setRecordId(?string $recordId): void
    {
        $this->recordId = $recordId;
    }

    public function setNextStep(Response\NextStep $nextStep): void
    {
        $this->nextStep = $nextStep;
    }

    public function setBaseEntity(?object $baseFatEntity): void
    {
        $this->baseEntity = $baseFatEntity;
    }
}
