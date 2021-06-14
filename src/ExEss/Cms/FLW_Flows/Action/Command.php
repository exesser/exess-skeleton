<?php
namespace ExEss\Cms\FLW_Flows\Action;

use ExEss\Cms\Generic\ToArray;

class Command implements ToArray, \JsonSerializable
{
    public const COMMAND_TYPE_OPEN_LINK = 'openLink';
    public const COMMAND_TYPE_OPEN_MODAL = 'openModal';
    public const COMMAND_TYPE_OPEN_MINI_GUIDANCE = 'openMiniGuidance';
    public const COMMAND_TYPE_RELOAD_PAGE = 'reloadPage';
    public const COMMAND_TYPE_NAVIGATE = 'navigate';
    public const COMMAND_TYPE_RELOAD_LIST = 'reloadList';
    public const COMMAND_TYPE_NOTHING = 'nothing';
    public const COMMAND_TYPE_PREVIOUS_PAGE = 'previousPage';
    public const COMMAND_TYPE_CHANGE_STEP = 'changeStep';

    protected string $command;

    protected ?string $backendCommand = null;

    protected Arguments $arguments;

    protected array $relatedBeans;

    protected array $params;

    public function __construct(
        string $command,
        Arguments $arguments,
        ?string $backendCommand = null,
        array $relatedBeans = [],
        array $params = []
    ) {
        $this->command = $command;
        $this->arguments = $arguments;
        $this->backendCommand = $backendCommand;
        $this->relatedBeans = $relatedBeans;
        $this->params = $params;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getArguments(): Arguments
    {
        return $this->arguments;
    }

    public function setArguments(Arguments $arguments): Command
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function getBackendCommand(): ?string
    {
        return $this->backendCommand;
    }

    public function getRelatedBeans(): array
    {
        return $this->relatedBeans;
    }

    public function setRelatedBeans(array $relatedBeans): Command
    {
        $this->relatedBeans = $relatedBeans;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): Command
    {
        $this->params = $params;

        return $this;
    }

    public function toArray(): array
    {
        // @todo check why $this->backendCommand is not in the returned array
        return [
            'command' => $this->command,
            'arguments' => $this->arguments->toArray(),
            'relatedBeans' => $this->relatedBeans,
            'params' => $this->params
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
