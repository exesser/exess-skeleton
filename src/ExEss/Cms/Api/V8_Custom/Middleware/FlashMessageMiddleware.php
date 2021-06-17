<?php
namespace ExEss\Cms\Api\V8_Custom\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;

class FlashMessageMiddleware
{
    private FlashMessageContainer $flashMessageContainer;

    public function __construct(
        FlashMessageContainer $flashMessageContainer
    ) {
        $this->flashMessageContainer = $flashMessageContainer;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        $response = $next($request, $response);

        if ($this->flashMessageContainer->count() < 1) {
            return $response;
        }

        $newBody = \json_decode((string) $response->getBody(), true);
        $newBody['flashMessages'] = $this->flashMessageContainer->getFlashMessages();
        $this->flashMessageContainer->reset();

        $body = new \Slim\Http\Body(\fopen('php://temp', 'r+'));
        $body->write(\json_encode($newBody, \JSON_PRETTY_PRINT));

        return $response->withBody($body);
    }
}
