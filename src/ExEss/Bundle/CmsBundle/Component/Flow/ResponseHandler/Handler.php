<?php

namespace ExEss\Bundle\CmsBundle\Component\Flow\ResponseHandler;

use ExEss\Bundle\CmsBundle\Component\Flow\Event\FlowEvent;

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
