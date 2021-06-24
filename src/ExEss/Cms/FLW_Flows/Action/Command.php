<?php
namespace ExEss\Cms\FLW_Flows\Action;

class Command implements \JsonSerializable
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

    public function getBackendCommand(): ?string
    {
        return $this->backendCommand;
    }

    public function getRelatedBeans(): array
    {
        return $this->relatedBeans;
    }

    public function setRelatedBeans(array $relatedBeans): void
    {
        $this->relatedBeans = $relatedBeans;
    }

    public function jsonSerialize(): array
    {
        return [
            'command' => $this->command,
            'arguments' => $this->arguments->toArray(),
            'relatedBeans' => $this->relatedBeans,
            'params' => $this->params,
        ];
    }
}
