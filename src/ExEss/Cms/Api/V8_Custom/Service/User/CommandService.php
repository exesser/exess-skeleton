<?php

namespace ExEss\Cms\Api\V8_Custom\Service\User;

use ExEss\Cms\Entity\User;
use ExEss\Cms\Component\Flow\Action\Command;
use ExEss\Cms\Users\Service\GuidanceRecoveryService;

class CommandService
{
    private GuidanceRecoveryService $guidanceRecoveryService;

    public function __construct(GuidanceRecoveryService $guidanceRecoveryService)
    {
        $this->guidanceRecoveryService = $guidanceRecoveryService;
    }

    public function getRedirectCommandForUser(User $user): ?Command
    {
        if (($user->isAgent() || $user->isAdmin()) && $user->hasRecoveryData()) {
            return $this->guidanceRecoveryService->getCommand();
        }

        return null;
    }
}
