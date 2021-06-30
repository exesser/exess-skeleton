<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Handler;

interface SaveHandlerInterface
{
    public static function shouldHandle(FlowData $data): bool;

    public function handle(FlowData $data): void;
}
