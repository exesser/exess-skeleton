<?php

namespace ExEss\Cms\Api\V8_Custom\Service\User;

use ExEss\Cms\Api\V8_Custom\Service\SimpleActionFactory;
use ExEss\Cms\Entity\FlowAction;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Component\Flow\Action\Command;

class CommandService
{
    private SimpleActionFactory $actionFactory;

    public function __construct(SimpleActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    public function getRedirectCommandForUser(User $user): ?Command
    {
        if (($user->isAgent() || $user->isAdmin()) && $user->hasRecoveryData()) {
            return $this->actionFactory->getCommand(FlowAction::ACTION_MODAL_TO_GF_RECOVERY);
        }

        return null;
    }
}
