<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\User;

use ExEss\Bundle\CmsBundle\Entity\User;
use ExEss\Bundle\CmsBundle\Component\Flow\Action\Command;
use ExEss\Bundle\CmsBundle\Users\Service\GuidanceRecoveryService;

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
