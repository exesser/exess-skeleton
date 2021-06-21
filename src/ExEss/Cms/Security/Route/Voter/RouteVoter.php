<?php declare(strict_types=1);

namespace ExEss\Cms\Security\Route\Voter;

use Psr\Http\Message\ServerRequestInterface;
use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\SecurityGroups\SecurityGroupsRepository;

class RouteVoter implements VoterInterface
{
    public const WHITELISTED_ROUTES = [
        'exesscms_login',
        'exess_cms_user_current__invoke',
        // @todo remove these
        'exess_cms_slimfallback__invoke',
        'exess_cms_slimfallback__invoke_1',
    ];

    private Security $security;

    private SecurityGroupsRepository $securityGroupsRepository;

    public function __construct(Security $security, SecurityGroupsRepository $securityGroupsRepository)
    {
        $this->security = $security;
        $this->securityGroupsRepository = $securityGroupsRepository;
    }

    public function supports(ServerRequestInterface $request): bool
    {
        return true;
    }

    public function vote(ServerRequestInterface $request): bool
    {
        if (!($route = $request->getAttribute('_route'))) {
            throw new \InvalidArgumentException("The router should have run before this");
        }

        return $this->routeIsAllowed($request->getMethod(), $route);
    }

    private function routeIsAllowed(string $method, string $route): bool
    {
        $userGroupTypes = $this->securityGroupsRepository->getUserGroupTypes($method, $route);

        // these routes are always allowed
        if (\in_array($route, self::WHITELISTED_ROUTES, true)) {
            return true;
        }

        $currentUser = $this->security->getCurrentUser();

        // If we don't have a user, the call is not allowed (login calls were whitelisted)
        if (!$currentUser || $currentUser->getId() === null) {
            return false;
        }

        // all calls are allowed for admins
        if ($currentUser->isAdmin()) {
            return true;
        }

        if ($currentUser->hasMatchedUserGroup($userGroupTypes)) {
            return true;
        }

        return $this->securityGroupsRepository->hasMatchedSecurityGroups(
            $currentUser->getId(),
            $route,
            $method
        );
    }
}
