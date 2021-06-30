<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Http\Factory;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PsrFactory
{
    private PsrHttpFactory $factory;

    public function __construct()
    {
        $psr17Factory = new Psr17Factory();
        $this->factory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
    }

    public function createRequest(Request $request): ServerRequestInterface
    {
        return $this->factory->createRequest($request);
    }

    public function createResponse(Response $response): ResponseInterface
    {
        return $this->factory->createResponse($response);
    }
}
