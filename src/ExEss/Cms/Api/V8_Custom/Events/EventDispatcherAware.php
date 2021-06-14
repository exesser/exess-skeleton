<?php

namespace ExEss\Cms\Api\V8_Custom\Events;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface EventDispatcherAware
{
    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void;
}
