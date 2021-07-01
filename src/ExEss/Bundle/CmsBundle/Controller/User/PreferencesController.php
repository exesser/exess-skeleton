<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\User;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\Security;
use ExEss\Bundle\CmsBundle\Exception\NotAuthenticatedException;
use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
use ExEss\Bundle\CmsBundle\Service\UserService;
use Symfony\Component\Routing\Annotation\Route;

class PreferencesController
{
    private UserService $userService;

    private Security $security;

    public function __construct(Security $security, UserService $userService)
    {
        $this->userService = $userService;
        $this->security = $security;
    }

    /**
     * @Route("/user/preferences", methods={"GET"})
     */
    public function __invoke(): SuccessResponse
    {
        if (null === $user = $this->security->getCurrentUser()) {
            throw new NotAuthenticatedException('Token not found');
        }

        return new SuccessResponse($this->userService->getPreferencesData($user));
    }
}
