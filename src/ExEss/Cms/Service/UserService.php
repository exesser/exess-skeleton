<?php declare(strict_types=1);

namespace ExEss\Cms\Service;

use ExEss\Cms\Api\V8_Custom\Service\User\CommandService;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Users\PreferencesData;

class UserService
{
    private string $fallbackLocale;

    private CommandService $commandService;

    public function __construct(string $fallbackLocale, CommandService $commandService)
    {
        $this->fallbackLocale = $fallbackLocale;
        $this->commandService = $commandService;
    }

    public function getPreferencesData(User $currentUser): PreferencesData
    {
        return new PreferencesData(
            $currentUser,
            $this->fallbackLocale,
            $this->commandService->getRedirectCommandForUser($currentUser)
        );
    }
}
