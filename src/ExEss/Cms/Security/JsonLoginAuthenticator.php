<?php declare(strict_types=1);

namespace ExEss\Cms\Security;

use ExEss\Cms\Api\V8_Custom\Events\AfterLoginEvent;
use ExEss\Cms\Api\V8_Custom\Events\UserEvents;
use ExEss\Cms\Api\V8_Custom\Service\User\TokenService;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class JsonLoginAuthenticator implements AuthenticationSuccessHandlerInterface
{
    private TokenService $tokenService;
    private EventDispatcher $dispatcher;

    public function __construct(TokenService $tokenService, EventDispatcher $dispatcher)
    {
        $this->tokenService = $tokenService;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        $this->dispatcher->dispatch(
            new AfterLoginEvent(
                $token->getUser(),
                $this->tokenService->generateToken($token->getUser()->getUsername())
            ),
            UserEvents::AFTER_LOGIN
        );

        return null;
    }
}
