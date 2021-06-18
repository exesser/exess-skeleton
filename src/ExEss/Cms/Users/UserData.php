<?php declare(strict_types=1);

namespace ExEss\Cms\Users;

use ExEss\Cms\Entity\User;
use ExEss\Cms\FLW_Flows\Action\Command;
use JsonSerializable;

class UserData implements JsonSerializable
{
    private string $username;

    private string $preferredLanguage;

    private ?Command $command;

    public function __construct(User $user, string $fallbackLocale, ?Command $command)
    {
        $this->username = $user->getUserIdentifier() ?? '';
        $this->preferredLanguage = $user->getPreferredLocale() ?? $fallbackLocale;
        $this->command = $command;
    }

    public function jsonSerialize(): array
    {
        return \array_filter(\get_object_vars($this));
    }
}
