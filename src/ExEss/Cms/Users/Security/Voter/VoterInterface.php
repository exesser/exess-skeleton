<?php

namespace ExEss\Cms\Users\Security\Voter;

use ExEss\Cms\Api\V8_Custom\Service\Security;

interface VoterInterface
{
    public function supports(string $attribute, object $subject, Security $security): bool;
    public function voteOnAttribute(string $attribute, object $subject, Security $security): bool;
}
