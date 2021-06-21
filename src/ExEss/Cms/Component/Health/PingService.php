<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Health;

class PingService
{
    public function getResult(): array
    {
        return ['result' => true];
    }
}
