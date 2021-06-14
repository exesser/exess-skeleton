<?php declare(strict_types=1);

namespace ExEss\Cms\Http;

use Symfony\Contracts\Service\ResetInterface;

class Cookies extends \Slim\Http\Cookies implements ResetInterface
{
    public function reset(): void
    {
        $this->responseCookies = [];
        $this->requestCookies = [];
    }
}
