<?php

namespace ExEss\Cms\Users\Security\Route\Voter;

use Slim\Http\Request;
use Slim\Route;

interface VoterInterface
{
    public function supports(Request $request, Route $route): bool;
    public function vote(Request $request, Route $route): bool;
}
