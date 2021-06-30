<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Adapter\Transport;

use ExEss\Bundle\CmsBundle\Adapter\Request\Request;
use ExEss\Bundle\CmsBundle\Adapter\Response\Response;

interface Transport
{
    public function request(Request $request, ?\Closure $responseHandler = null): Response;
}
