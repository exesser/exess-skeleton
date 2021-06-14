<?php

namespace ExEss\Cms\Api\V8_Custom\Service\FlashMessages;

use Symfony\Contracts\Service\ResetInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This is a class to which you can add flash messages
 * which on their turn will be send to DWP.
 */
class FlashMessageContainer implements \Countable, ResetInterface
{
    private array $flashMessages = [];

    private TranslatorInterface $translator;

    private static ?self $instance = null;

    public static function get(TranslatorInterface $translator): self
    {
        if (!self::$instance) {
            self::$instance = new self($translator);
        }

        return self::$instance;
    }

    private function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function addFlashMessage(FlashMessage $flashMessage): self
    {
        foreach ($this->flashMessages as $existing) {
            if (true === $existing->equals($flashMessage)) {
                $existing->addCount();
                return $this;
            }
        }

        $this->flashMessages[] = $flashMessage;

        return $this;
    }

    /**
     * @return FlashMessage[]
     */
    public function getFlashMessages(?string $group = null): array
    {
        if (!empty($group)) {
            return \array_filter($this->flashMessages, function (FlashMessage $msg) use ($group) {
                return $msg->getGroup() === $group;
            });
        }

        return $this->flashMessages;
    }

    public function count(): int
    {
        return \count($this->getFlashMessages());
    }

    public function reset(): void
    {
        $this->flashMessages = [];
    }
}
