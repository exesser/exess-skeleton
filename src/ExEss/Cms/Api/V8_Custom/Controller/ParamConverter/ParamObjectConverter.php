<?php

namespace ExEss\Cms\Api\V8_Custom\Controller\ParamConverter;

use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\V8_Custom\Params\AbstractParams;

class ParamObjectConverter extends AbstractConverter
{
    public function __invoke(AbstractParams $params, Request $request, Response $response, callable $next): Response
    {
        $arguments = $this->getRequestParameters($request);

        if (!$arguments) {
            return $response->withJson(
                ['message' => 'unable to create params object data found in request'],
                400
            );
        }

        try {
            $params->configure($arguments);
        } catch (\Exception $e) {
            return $this->handleException($params, $response, $e);
        }
        $request = $request->withAttribute('params', $params);

        return $next($request, $response);
    }

    protected function handleException(AbstractParams $params, Response $response, \Exception $e): Response
    {
        return $response->withJson(['message' => $e->getMessage()], 400);
    }
}
