<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages;

interface FlashMessageContainerAware
{
    public function setFlashMessageContainer(FlashMessageContainer $flashMessageContainer): void;
}
