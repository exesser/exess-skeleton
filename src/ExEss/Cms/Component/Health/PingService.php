<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Health;

class PingService
{
    public function getResult(): string
    {
        return '<?xml version="1.0"?><rs-response><result>true</result></rs-response>';
    }
}
