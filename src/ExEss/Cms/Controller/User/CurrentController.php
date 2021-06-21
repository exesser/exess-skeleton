<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\User;

use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\Exception\NotAuthenticatedException;
use ExEss\Cms\Http\SuccessResponse;
use ExEss\Cms\Service\UserService;
use Symfony\Component\Routing\Annotation\Route;

class CurrentController
{
    private UserService $userService;

    private Security $security;

    public function __construct(Security $security, UserService $userService)
    {
        $this->userService = $userService;
        $this->security = $security;
    }

    /**
     * @Route("/Api/user/current", methods={"GET"})
     */
    public function __invoke(): SuccessResponse
    {
        if (null === $user = $this->security->getCurrentUser()) {
            throw new NotAuthenticatedException('Token not found');
        }

        return new SuccessResponse($this->userService->getDataFor($user));
    }
}
