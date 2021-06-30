<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\AfterSave\Handler;

use ExEss\Bundle\CmsBundle\Component\Flow\AfterSave\AfterSaveData;

interface AfterSaveHandlerInterface
{
    public static function supportsFlow(AfterSaveData $data): bool;

    public function handle(AfterSaveData $data): void;
}
