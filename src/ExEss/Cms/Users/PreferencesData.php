<?php declare(strict_types=1);

namespace ExEss\Cms\Users;

use ExEss\Cms\Entity\User;
use ExEss\Cms\FLW_Flows\Action\Command;
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
