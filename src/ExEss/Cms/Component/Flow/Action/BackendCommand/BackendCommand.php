<?php
namespace ExEss\Cms\Component\Flow\Action\BackendCommand;

use ExEss\Cms\Component\Flow\Response\Model;

interface BackendCommand
{
    public function execute(array $recordIds, ?Model $model = null): void;
}
