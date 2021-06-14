<?php
namespace ExEss\Cms\Api\V8_Custom\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ExEss\Cms\Api\V8_Custom\Service\RequestResponseLogger;

/**
 * log requests and responses
 */
class LoggingMiddleware
{
    private RequestResponseLogger $requestResponseLogger;

    public function __construct(RequestResponseLogger $requestResponseLogger)
    {
        $this->requestResponseLogger = $requestResponseLogger;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        $this->requestResponseLogger->logRequest($request);

        $response = $next($request, $response);

        $this->requestResponseLogger->logResponse($request, $response);

        return $response;
    }
}
