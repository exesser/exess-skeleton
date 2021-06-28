<?php declare(strict_types=1);

namespace ExEss\Cms\Security\Route\Voter;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Entity\SecurityGroupApi;
use Psr\Http\Message\ServerRequestInterface;
use ExEss\Cms\Api\V8_Custom\Service\Security;

class RouteVoter implements VoterInterface
{
    public const WHITELISTED_ROUTES = [
        'exesscms_login',
        'exess_cms_user_preferences__invoke',
    ];

    private Security $security;

    private EntityManagerInterface $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
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
        $repository = $this->em->getRepository(SecurityGroupApi::class);
        $userGroupTypes = $repository->getUserGroupTypes($method, $route);

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

        return $repository->hasMatchedSecurityGroups(
            $currentUser,
            $route,
            $method
        );
    }
}
