<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Security;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\User\TokenService;
use ExEss\Bundle\CmsBundle\Entity\User;
use ExEss\Bundle\CmsBundle\Http\ErrorResponse;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtTokenAuthenticator extends AbstractGuardAuthenticator
{
    private TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function getJwtToken(Request $request): ?string
    {
        $authorization = $request->headers->get('Authorization');
        return \explode(' ', $authorization ?? '')[1] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): bool
    {
        return ($credentials = $this->getCredentials($request)) !== null || $credentials === 'null';
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request): ?stdClass
    {
        if (($token = $this->getJwtToken($request)) === null) {
            return null;
        }

        return $this->tokenService->decodeToken($token);
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        if (null === $credentials) {
            return null;
        }

        return $userProvider->loadUserByUsername($credentials->userId);
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new Response(
            \strtr($exception->getMessageKey(), $exception->getMessageData()),
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new ErrorResponse(
            Response::HTTP_UNAUTHORIZED,
            'Authentication Required'
        );
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
