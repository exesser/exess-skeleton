<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Adapter;

use ExEss\Bundle\CmsBundle\Adapter\Request\Request;
use ExEss\Bundle\CmsBundle\Adapter\Response\Response;

interface RequestAdapter
{
    public function doRequest(Request $request): Response;
    public function doRequestWithParameters(array $parameters, array $headers = []): Response;
}
