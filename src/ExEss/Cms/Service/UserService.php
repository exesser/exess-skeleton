<?php declare(strict_types=1);

namespace ExEss\Cms\Service;

use ExEss\Cms\Api\V8_Custom\Service\User\CommandService;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Users\UserData;

class UserService
{
    private string $fallbackLocale;

    private CommandService $commandService;

    public function __construct(string $fallbackLocale, CommandService $commandService)
    {
        $this->fallbackLocale = $fallbackLocale;
        $this->commandService = $commandService;
    }

    public function getDataFor(User $currentUser): UserData
    {
        return new UserData(
            $currentUser,
            $this->fallbackLocale,
            $this->commandService->getRedirectCommandForUser($currentUser)
        );
    }
}
