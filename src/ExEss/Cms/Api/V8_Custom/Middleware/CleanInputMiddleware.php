<?php

namespace ExEss\Cms\Api\V8_Custom\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ExEss\Cms\Api\V8_Custom\Service\DataCleaner;

class CleanInputMiddleware
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {

        // dont clean input when doing flow updates, which can contain WYSIWYG
        if (\strpos((string)$request->getUri(), '/Api/V8_Custom/Flow/') !== false) {
            return $next($request, $response);
        }

        $cleanedRequest = $request->withParsedBody(DataCleaner::clean($request->getParsedBody()));

        return $next($cleanedRequest, $response);
    }
}
