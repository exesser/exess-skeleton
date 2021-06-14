<?php declare(strict_types=1);

namespace ExEss\Cms\Adapter;

use ExEss\Cms\Adapter\Request\Request;
use ExEss\Cms\Adapter\Response\Response;

interface RequestAdapter
{
    public function doRequest(Request $request): Response;
    public function doRequestWithParameters(array $parameters, array $headers = []): Response;
}
