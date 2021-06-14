<?php

namespace ExEss\Cms\FLW_Flows\ResponseHandler;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;

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
