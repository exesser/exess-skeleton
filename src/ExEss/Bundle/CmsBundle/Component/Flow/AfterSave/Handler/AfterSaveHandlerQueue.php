<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\AfterSave\Handler;

use ExEss\Bundle\CmsBundle\Component\Flow\AfterSave\AfterSaveData;

class AfterSaveHandlerQueue
{
    /**
     * @var iterable|AfterSaveHandlerInterface[]
     */
    private iterable $handlers;

    public function __construct(iterable $handlers)
    {
        $this->handlers = $handlers;
    }

    public function apply(AfterSaveData $data): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler::supportsFlow($data)) {
                $handler->handle($data);
            }
        }
    }
}
