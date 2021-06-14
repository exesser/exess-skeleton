<?php
namespace ExEss\Cms\FLW_Flows\Handler;

abstract class AbstractSaveHandler implements SaveHandlerInterface
{
    final public function handle(FlowData $data): void
    {
        if (!static::shouldHandle($data)) {
            return;
        }
        $this->doHandle($data);
    }

    abstract public static function shouldHandle(FlowData $data): bool;

    abstract protected function doHandle(FlowData $data): void;
}
