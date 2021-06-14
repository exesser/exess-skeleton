<?php
namespace ExEss\Cms\FLW_Flows\Handler;

interface SaveHandlerInterface
{
    public static function shouldHandle(FlowData $data): bool;

    public function handle(FlowData $data): void;
}
