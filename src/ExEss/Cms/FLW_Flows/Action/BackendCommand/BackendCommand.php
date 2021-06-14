<?php
namespace ExEss\Cms\FLW_Flows\Action\BackendCommand;

use ExEss\Cms\FLW_Flows\Response\Model;

interface BackendCommand
{
    public function execute(array $recordIds, ?Model $model = null): void;
}
