<?php
namespace ExEss\Cms\Api\V8_Custom\Middleware;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Exception\NotAuthorizedException;
use ExEss\Cms\Users\Security\Route\DecisionManager;

class SecurityMiddleware
{
    private DecisionManager $decisionManager;

    public function __construct(DecisionManager $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    public function __invoke(
        Request $request,
        Response $response,
        callable $next
    ): ResponseInterface {
        if ($this->decisionManager->hasAccess($request, $request->getAttribute('route'))) {
            return $next($request, $response);
        }

        throw new NotAuthorizedException();
    }
}
