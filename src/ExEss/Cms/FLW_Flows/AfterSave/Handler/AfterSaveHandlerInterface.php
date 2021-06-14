<?php
namespace ExEss\Cms\FLW_Flows\AfterSave\Handler;

use ExEss\Cms\FLW_Flows\AfterSave\AfterSaveData;

interface AfterSaveHandlerInterface
{
    public static function supportsFlow(AfterSaveData $data): bool;

    public function handle(AfterSaveData $data): void;
}
