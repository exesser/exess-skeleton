<?php

namespace ExEss\Cms\Api\V8_Custom\Service\User;

use ExEss\Cms\Api\V8_Custom\Service\SimpleActionFactory;
use ExEss\Cms\Entity\FlowAction;
use ExEss\Cms\Entity\User;
use ExEss\Cms\FLW_Flows\Action\Arguments;
use ExEss\Cms\FLW_Flows\Action\Command;

class CommandService
{
    private SimpleActionFactory $actionFactory;

    private RedirectPathCookieService $cookies;

    public function __construct(SimpleActionFactory $actionFactory, RedirectPathCookieService $cookies)
    {
        $this->actionFactory = $actionFactory;
        $this->cookies = $cookies;
    }

    public function getRedirectCommandForUser(User $user): ?Command
    {
        $intendedPath = $this->cookies->get();

        // immediately unset intended full path cookie - needs to be sent in the response
        $this->cookies->invalidate();

        if (($user->isAgent() || $user->isAdmin()) && $user->hasRecoveryData()) {
            return $this->actionFactory->getCommand(FlowAction::ACTION_MODAL_TO_GF_RECOVERY);
        }

        if (($user->isAgent() || $user->isAdmin()) && !empty($intendedPath)) {
            return self::getOpenLinkCommand($intendedPath);
        }

        return null;
    }

    public static function getOpenLinkCommand(string $link): Command
    {
        $arguments = new Arguments();
        $arguments->link = $link;

        return new Command(Command::COMMAND_TYPE_OPEN_LINK, $arguments);
    }
}
