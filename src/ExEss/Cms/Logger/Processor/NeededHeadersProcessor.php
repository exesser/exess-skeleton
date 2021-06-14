<?php declare(strict_types=1);

namespace ExEss\Cms\Logger\Processor;

use ExEss\Cms\Component\Logger\Processor\NeededHeadersProcessor as BaseProcessor;
use ExEss\Cms\Component\Session\Headers;
use ExEss\Cms\Component\Session\User\UserInterface;
use ExEss\Cms\Api\V8_Custom\Service\Security;

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
