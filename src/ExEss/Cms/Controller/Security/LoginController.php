<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Security;

use ExEss\Cms\Api\V8_Custom\Service\User\RefreshTokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LoginController
{
    private RefreshTokenService $tokenService;

    private TokenStorageInterface $tokenStorage;

    public function __construct(RefreshTokenService $tokenService, TokenStorageInterface $tokenStorage)
    {
        $this->tokenService = $tokenService;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/Api/login", name="exesscms_login", methods={"POST"})
     */
    public function __invoke(): Response
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return new Response('Token not found', Response::HTTP_UNAUTHORIZED);
        }

        if (!\is_object($user = $token->getUser())) {
            return new Response('Not logged in', Response::HTTP_UNAUTHORIZED);
        }

        $jwt = $this->tokenService->generateToken($user->getUsername());

        return new JsonResponse(['token' => $jwt], Response::HTTP_OK);
    }
}
