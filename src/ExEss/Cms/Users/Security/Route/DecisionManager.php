<?php

namespace ExEss\Cms\Users\Security\Route;

use Slim\Http\Request;
use Slim\Route;
use ExEss\Cms\Users\Security\Route\Voter\VoterInterface;

class DecisionManager
{
    /**
     * @var iterable|VoterInterface[]
     */
    private iterable $voters = [];

    public function __construct(iterable $voters)
    {
        $this->voters = $voters;
    }

    public function hasAccess(Request $request, Route $route): bool
    {
        foreach ($this->voters as $voter) {
            if ($voter instanceof VoterInterface
                && $voter->supports($request, $route)
                && !$voter->vote($request, $route)
            ) {
                return false;
            }
        }

        return true;
    }
}
