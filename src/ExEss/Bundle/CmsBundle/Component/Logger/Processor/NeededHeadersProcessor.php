<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Logger\Processor;

use ExEss\Bundle\CmsBundle\Component\Session\Headers;
use ExEss\Bundle\CmsBundle\Component\Session\User\UserInterface;

class NeededHeadersProcessor
{
    private Headers $neededHeaders;

    public function __construct(Headers $neededHeaders, ?UserInterface $user = null)
    {
        $this->neededHeaders = $neededHeaders;
        if ($user instanceof UserInterface) {
            $this->neededHeaders->setUser($user);
        }
    }

    public function __invoke(array $record): array
    {
        foreach ($this->neededHeaders as $key => $header) {
            $record['extra'][$key] = $header;
        }

        return $record;
    }

    protected function getNeededHeaders(): Headers
    {
        return $this->neededHeaders;
    }
}
