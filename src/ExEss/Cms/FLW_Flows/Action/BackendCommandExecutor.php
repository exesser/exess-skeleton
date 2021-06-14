<?php

namespace ExEss\Cms\FLW_Flows\Action;

use ExEss\Cms\FLW_Flows\Action\BackendCommand\BackendCommand;
use ExEss\Cms\FLW_Flows\Response\Model;

class BackendCommandExecutor
{
    /**
     * @var BackendCommand[]
     */
    private array $commands;

    /**
     * @param array|BackendCommand[] $commands
     */
    public function __construct(iterable $commands)
    {
        $this->commands = $commands instanceof \Traversable ? \iterator_to_array($commands): $commands;
    }

    /**
     * Executes a BackendCommand
     *
     * @throws \InvalidArgumentException When an incorrect back-end command is submitted.
     */
    public function execute(Command $command, array $recordIds, ?Model $model = null): void
    {
        $alias = $command->getBackendCommand();

        if (!$alias) {
            return;
        }

        if (!isset($this->commands[$alias])) {
            throw new \InvalidArgumentException(\sprintf(
                'No BackendCommand for alias "%s" found in command %s',
                $alias,
                $command->getCommand()
            ));
        }

        /** @var BackendCommand $backendCommand */
        $backendCommand = $this->commands[$alias];

        if (!$backendCommand instanceof BackendCommand) {
            throw new \InvalidArgumentException(\sprintf(
                'Expected a BackendCommand, got a %s in command %s',
                \get_class($backendCommand),
                $command->getCommand()
            ));
        }

        $backendCommand->execute($recordIds, $model);
    }
}
