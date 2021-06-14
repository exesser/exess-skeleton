<?php

namespace ExEss\Cms\Users\Security\Route\Voter;

use ExEss\Cms\Doctrine\Type\HttpMethod;
use Slim\Http\Request;
use FastRoute\Dispatcher;
use Slim\Interfaces\RouteInterface;
use Slim\Route;
use Slim\Router;
use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\SecurityGroups\SecurityGroupsRepository;

class RouteVoter implements VoterInterface
{
    public const WHITELISTED_ROUTES = [
        '/V8_Custom/user/current',
        '/V8_Custom/check/ping',
        '/V8_Custom/check/health',
    ];

    public const OPTIONS_PATTERN = '/{routes:.+}';

    private Security $security;

    private SecurityGroupsRepository $securityGroupsRepository;

    private Router $router;

    public function __construct(Security $security, SecurityGroupsRepository $securityGroupsRepository, Router $router)
    {
        $this->security = $security;
        $this->securityGroupsRepository = $securityGroupsRepository;
        $this->router = $router;
    }

    public function supports(Request $request, Route $route): bool
    {
        return true;
    }

    public function vote(Request $request, Route $route): bool
    {
        $preflight = false;
        $route = $request->getAttribute('route');
        if ($route->getPattern() === self::OPTIONS_PATTERN) {
            $route = $this->findActualRoute($request);
            $preflight = true;
        }

        if ($route !== null
            && !$this->routeIsAllowed(
                $request->getMethod(),
                $route->getPattern(),
                $preflight
            )
        ) {
            return false;
        }

        return true;
    }

    private function findActualRoute(Request $request): ?RouteInterface
    {
        $method = $request->getHeader('Access-Control-Request-Method')[0] ?? null;
        if (!\in_array($method, \array_keys(HttpMethod::getValues()), true)) {
            return null;
        }

        $clonedRequest = $request->withMethod($method);
        $routeInfo = $this->router->dispatch($clonedRequest);
        if ($routeInfo[0] === Dispatcher::FOUND) {
            return $this->router->lookupRoute($routeInfo[1]);
        }

        return null;
    }

    private function routeIsAllowed(string $method, string $route, bool $preflight): bool
    {
        $route = \substr($route, \strlen('/Api'));
        $userGroupTypes = $this->securityGroupsRepository->getUserGroupTypes($method, $route);

        // these routes are always allowed
        if (\in_array($route, self::WHITELISTED_ROUTES, true)) {
            return true;
        }

        // If we don't have a user, the call is not allowed (login calls were whitelisted)
        if (!$preflight && ($this->security->getCurrentUser() === null
            || $this->security->getCurrentUser()->getId() === null)
        ) {
            return false;
        }

        // all calls are allowed for admins
        if (!$preflight && $this->security->getCurrentUser()->isAdmin()) {
            return true;
        }

        // V8 calls are ONLY allowed for admins
        if (\strpos($route, '/V8/') !== false) {
            return false;
        }

        if ($preflight) {
            return true;
        }

        if ($this->security->getCurrentUser()->hasMatchedUserGroup($userGroupTypes)) {
            return true;
        }

        return $this->securityGroupsRepository->hasMatchedSecurityGroups(
            $this->security->getCurrentUser()->getId(),
            $route,
            $method
        );
    }
}
