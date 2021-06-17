<?php declare(strict_types=1);

namespace ExEss\Cms\Api\V8_Custom\Service\FlashMessages;

use Symfony\Contracts\Service\ResetInterface;

/**
 * This is a class to which you can add flash messages
 * which on their turn will be send to DWP.
 */
class FlashMessageContainer implements \Countable, ResetInterface
{
    /**
     * @var array|FlashMessage[]
     */
    private array $flashMessages = [];

    private static ?self $instance = null;

    public static function get(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function addFlashMessage(FlashMessage $flashMessage): void
    {
        foreach ($this->flashMessages as $existing) {
            if (true === $existing->equals($flashMessage)) {
                $existing->addCount();
                return;
            }
        }

        $this->flashMessages[] = $flashMessage;
    }

    /**
     * @return array|FlashMessage[]
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
        return \count($this->flashMessages);
    }

    public function reset(): void
    {
        $this->flashMessages = [];
    }
}
