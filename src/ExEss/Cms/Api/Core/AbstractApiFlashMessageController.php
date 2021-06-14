<?php

namespace ExEss\Cms\Api\Core;

use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use Symfony\Component\Translation\Translator;

abstract class AbstractApiFlashMessageController extends AbstractApiController
{
    private FlashMessageContainer $flashMessageContainer;

    protected ?Translator $translator;

    public function __construct(
        FlashMessageContainer $flashMessageContainer,
        ?Translator $translator = null
    ) {
        $this->flashMessageContainer = $flashMessageContainer;
        $this->translator = $translator;
    }

    protected function addFlashMessage(
        string $text,
        string $type = FlashMessage::TYPE_ERROR
    ): void {
        if ($this->translator instanceof Translator) {
            $text = $this->translator->trans($text, [], TranslationDomain::FLASH_MESSAGE);
        }

        $this->flashMessageContainer->addFlashMessage(new FlashMessage($text, $type));
    }
}
