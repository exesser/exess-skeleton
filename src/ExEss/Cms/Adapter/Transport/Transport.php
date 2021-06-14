<?php declare(strict_types=1);

namespace ExEss\Cms\Adapter\Transport;

use ExEss\Cms\Adapter\Request\Request;
use ExEss\Cms\Adapter\Response\Response;

interface Transport
{
    public function request(Request $request, ?\Closure $responseHandler = null): Response;
}
