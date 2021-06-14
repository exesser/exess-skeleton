<?php

namespace ExEss\Cms\Api\V8_Custom\Controller\ParamConverter;

use Psr\Container\ContainerInterface as Container;
use Slim\Http\Request;
use Slim\Http\Response;

class ParamObjectConverterFactory
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function create(string $serviceId, string $converter = ParamObjectConverter::class): callable
    {
        $container = $this->container;
        return function (
            Request $request,
            Response $response,
            callable $next
        ) use (
            $serviceId,
            $container,
            $converter
        ) {
            $params = $container->get($serviceId);

            return ($container->get($converter))($params, $request, $response, $next);
        };
    }
}
