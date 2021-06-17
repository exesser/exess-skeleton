<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Security;

use ExEss\Cms\Api\V8_Custom\Service\User\RefreshTokenService;
use ExEss\Cms\Exception\NotAuthenticatedException;
use ExEss\Cms\Http\SuccessResponse;
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
    public function __invoke(): SuccessResponse
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            throw new NotAuthenticatedException('Token not found');
        }

        if (!\is_object($user = $token->getUser())) {
            throw new NotAuthenticatedException('Not logged in');
        }

        $jwt = $this->tokenService->generateToken($user->getUsername());

        return new SuccessResponse(['token' => $jwt]);
    }
}
