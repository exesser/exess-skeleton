<?php

namespace ExEss\Cms\Component\Flow\ResponseHandler;

use ExEss\Cms\Component\Flow\Event\FlowEvent;

interface Handler
{
    /**
     * Defines if the decorator should interfere with the Response
     */
    public function shouldModify(FlowEvent $event): bool;

    /**
     * Modify the response object
     */
    public function __invoke(FlowEvent $event): void;
}
