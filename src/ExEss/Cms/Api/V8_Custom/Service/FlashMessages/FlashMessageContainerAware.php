<?php

namespace ExEss\Cms\Api\V8_Custom\Service\FlashMessages;

/**
 * Interface FlashMessageContainerAware
 */
interface FlashMessageContainerAware
{
    public function setFlashMessageContainer(FlashMessageContainer $flashMessageContainer): void;
}
