<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\User;

use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurrentController extends AbstractController
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
    public function __invoke(): Response
    {
        if (null === $user = $this->security->getCurrentUser()) {
            return new Response('Token not found', Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(['data' => $this->userService->getDataFor($user)], Response::HTTP_OK);
    }
}
