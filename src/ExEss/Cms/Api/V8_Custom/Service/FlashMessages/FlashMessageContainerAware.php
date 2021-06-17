<?php

namespace ExEss\Cms\Api\V8_Custom\Service\FlashMessages;

interface FlashMessageContainerAware
{
    public function setFlashMessageContainer(FlashMessageContainer $flashMessageContainer): void;
}
