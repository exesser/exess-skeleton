<?php

namespace ExEss\Cms\Api\V8_Custom\Controller\InvocationStrategy;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;

class ParamsInvocationStrategy implements InvocationStrategyInterface
{
    /**
     * @param array|callable         $callable
     *
     * @return mixed
     */
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ) {
        foreach ($routeArguments as $k => $v) {
            $request = $request->withAttribute($k, $v);
        }

        $controllerArgs = [$request, $response, $routeArguments];

        if ($request->getAttribute('params')) {
            $controllerArgs[] = $request->getAttribute('params');
        }

        return $callable(...$controllerArgs);
    }
}
