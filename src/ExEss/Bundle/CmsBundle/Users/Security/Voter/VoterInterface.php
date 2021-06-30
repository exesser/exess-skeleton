<?php

namespace ExEss\Bundle\CmsBundle\Users\Security\Voter;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\Security;

interface VoterInterface
{
    public function supports(string $attribute, object $subject, Security $security): bool;
    public function voteOnAttribute(string $attribute, object $subject, Security $security): bool;
}
