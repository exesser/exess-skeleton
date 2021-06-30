<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Security\Route;

use Psr\Http\Message\ServerRequestInterface;
use ExEss\Bundle\CmsBundle\Security\Route\Voter\VoterInterface;

class DecisionManager
{
    /**
     * @var iterable|VoterInterface[]
     */
    private iterable $voters;

    public function __construct(iterable $voters)
    {
        $this->voters = $voters;
    }

    public function hasAccess(ServerRequestInterface $request): bool
    {
        foreach ($this->voters as $voter) {
            if ($voter instanceof VoterInterface
                && $voter->supports($request)
                && !$voter->vote($request)
            ) {
                return false;
            }
        }

        return true;
    }
}
