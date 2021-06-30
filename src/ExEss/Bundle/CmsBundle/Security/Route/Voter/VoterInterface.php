<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Security\Route\Voter;

use Psr\Http\Message\ServerRequestInterface;

interface VoterInterface
{
    public function supports(ServerRequestInterface $request): bool;
    public function vote(ServerRequestInterface $request): bool;
}
