<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\User;

use ExEss\Cms\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CurrentController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;

    private UserService $userService;

    public function __construct(TokenStorageInterface $tokenStorage, UserService $userService)
    {
        $this->tokenStorage = $tokenStorage;
        $this->userService = $userService;
    }

    /**
     * @Route("/Api/user/current", methods={"GET"})
     */
    public function __invoke(): Response
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return new Response('Token not found', Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(['data' => $this->userService->getData($token)], Response::HTTP_OK);
    }
}
