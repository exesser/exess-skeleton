<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Logger\Processor;

use ExEss\Cms\Component\Session\Headers;
use ExEss\Cms\Component\Session\User\UserInterface;

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
