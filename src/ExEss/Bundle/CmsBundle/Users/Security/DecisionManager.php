<?php

namespace ExEss\Bundle\CmsBundle\Users\Security;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\Security;
use ExEss\Bundle\CmsBundle\Users\Security\Voter\VoterInterface;

class DecisionManager
{
    private Security $security;

    /**
     * @var iterable|VoterInterface[]
     */
    private iterable $voters = [];

    public function __construct(Security $security, iterable $voters)
    {
        $this->security = $security;
        $this->voters = $voters;
    }

    public function hasAccess(string $attribute, object $subject): bool
    {
        foreach ($this->voters as $voter) {
            if ($voter instanceof VoterInterface
                && $voter->supports($attribute, $subject, $this->security)
                && $voter->voteOnAttribute($attribute, $subject, $this->security)
            ) {
                return true;
            }
        }

        return false;
    }
}
