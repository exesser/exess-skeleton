<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Users;

use ExEss\Bundle\CmsBundle\Entity\User;
use ExEss\Bundle\CmsBundle\Component\Flow\Action\Command;
use JsonSerializable;

class PreferencesData implements JsonSerializable
{
    private string $preferredLanguage;

    private ?Command $command;

    public function __construct(User $user, string $fallbackLocale, ?Command $command)
    {
        $this->preferredLanguage = $user->getPreferredLocale() ?? $fallbackLocale;
        $this->command = $command;
    }

    public function jsonSerialize(): array
    {
        return \array_filter(\get_object_vars($this));
    }
}
