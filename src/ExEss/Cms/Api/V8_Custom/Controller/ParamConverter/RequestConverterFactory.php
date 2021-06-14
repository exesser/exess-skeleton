<?php

namespace ExEss\Cms\Api\V8_Custom\Controller\ParamConverter;

use Slim\Http\Request;
use Slim\Http\Response;

class RequestConverterFactory
{
    public function create(array $mapping): callable
    {
        return function (Request $request, Response $response, callable $next) use ($mapping) {
            return (new RequestConverter($mapping))($request, $response, $next);
        };
    }
}
