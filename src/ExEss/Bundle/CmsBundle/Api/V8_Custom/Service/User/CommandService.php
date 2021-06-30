<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\User;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\SimpleActionFactory;
use ExEss\Bundle\CmsBundle\Entity\FlowAction;
use ExEss\Bundle\CmsBundle\Entity\User;
use ExEss\Bundle\CmsBundle\Component\Flow\Action\Command;

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
