<?php

namespace ExEss\Cms\Api\V8_Custom\Controller\User;

use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\Api\V8_Custom\Service\User\CommandService;
use ExEss\Cms\Api\V8_Custom\Service\User\UserService;

class CurrentController extends AbstractApiController
{
    private Security $security;

    private UserService $userService;

    private CommandService $commandService;

    public function __construct(
        Security $security,
        UserService $userService,
        CommandService $commandService
    ) {
        $this->security = $security;
        $this->userService = $userService;
        $this->commandService = $commandService;
    }

    public function __invoke(Request $req, Response $res, array $args): Response
    {
        $currentUser = $this->security->getCurrentUser();
        if ($currentUser === null) {
            throw new \InvalidArgumentException('User not found');
        }

        $userData = $this->userService->getData($currentUser);

        if (null !== ($command = $this->commandService->getRedirectCommandForUser($currentUser))) {
            $userData['command'] = $command->toArray();
        }

        return $this->generateResponse(
            $res,
            200,
            $userData
        );
    }
}
