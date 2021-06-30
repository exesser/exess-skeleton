<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Logger\Processor;

use ExEss\Bundle\CmsBundle\Component\Logger\Processor\NeededHeadersProcessor as BaseProcessor;
use ExEss\Bundle\CmsBundle\Component\Session\Headers;
use ExEss\Bundle\CmsBundle\Component\Session\User\UserInterface;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\Security;

class NeededHeadersProcessor extends BaseProcessor
{
    private Security $security;

    public function __construct(Headers $headers, Security $security)
    {
        parent::__construct($headers);
        $this->security = $security;
    }

    public function __invoke(array $record): array
    {
        if ($this->security->getCurrentUser() instanceof UserInterface
            && !empty($this->security->getCurrentUser()->getId())
        ) {
            $this->getNeededHeaders()->setUser($this->security->getCurrentUser());
        }
        return parent::__invoke($record);
    }
}
