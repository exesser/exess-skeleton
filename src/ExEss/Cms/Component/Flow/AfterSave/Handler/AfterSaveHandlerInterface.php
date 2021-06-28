<?php
namespace ExEss\Cms\Component\Flow\AfterSave\Handler;

use ExEss\Cms\Component\Flow\AfterSave\AfterSaveData;

interface AfterSaveHandlerInterface
{
    public static function supportsFlow(AfterSaveData $data): bool;

    public function handle(AfterSaveData $data): void;
}
