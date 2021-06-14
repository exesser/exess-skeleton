<?php

namespace ExEss\Cms\Api\V8_Custom\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Cms\Dictionary\Response;
use ExEss\Cms\Exception\EmailForExportNotFoundException;
use ExEss\Cms\Exception\ExternalListFetchException;
use ExEss\Cms\Exception\NotAllowedException;
use ExEss\Cms\Exception\NotAuthorizedException;
use ExEss\Cms\Exception\NotFoundException;
use ExEss\Cms\FLW_Flows\Event\Exception\CommandException;
use ExEss\Cms\Logger\Logger;
use ExEss\Cms\Logger\Message\BusinessMessage;

class JsonErrorMiddleware
{
    private FlashMessageContainer $flashMessages;

    private Logger $logger;

    private bool $debug;

    public function __construct(
        FlashMessageContainer $flashMessages,
        Logger $logger,
        bool $debug
    ) {
        $this->flashMessages = $flashMessages;
        $this->logger = $logger;
        $this->debug = $debug;
    }

    /**
     * Invoke middleware to catch invalid arguments
     *
     * @param ResponseInterface|\Slim\Http\Response $response
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        try {
            $response = $next($request, $response);
        } catch (\Throwable $e) {
            $body['message'] = Response::MESSAGE_ERROR;

            switch (true) {
                case $e instanceof NotAuthorizedException:
                    $response = $response->withStatus(403, 'Forbidden');
                    $body = [];
                    $this->flashMessages->addFlashMessage(new FlashMessage(
                        'You don\'t have the rights to perform this action'
                    ));
                    break;
                case $e instanceof NotAllowedException:
                    $response = $response->withStatus(405);
                    $body['data'] =  Response::errorData(Response::TYPE_NOT_ALLOWED_EXCEPTION, $e->getMessage());
                    $this->logger->warning($e->getMessage() . \PHP_EOL . $e->getTraceAsString());
                    break;
                case $e instanceof NotFoundException:
                    $response = $response->withStatus(404);
                    $body['data'] = Response::errorData(Response::TYPE_NOT_FOUND_EXCEPTION, $e->getMessage());
                    $this->logger->warning($e->getMessage() . \PHP_EOL . $e->getTraceAsString());
                    break;
                case $e instanceof CommandException:
                case $e instanceof EmailForExportNotFoundException:
                    $this->flashMessages->addFlashMessage(new FlashMessage($e->getMessage()));
                    //fallthrough intended, we want to handle this like domainexception
                case $e instanceof \LogicException:
                case $e instanceof \DomainException:
                    $response = $response->withStatus(422);
                    $body['data'] = Response::errorData(Response::TYPE_DOMAIN_EXCEPTION, $e->getMessage());
                    $this->logger->error($e->getMessage() . \PHP_EOL . $e->getTraceAsString());
                    break;
                case $e instanceof ExternalListFetchException:
                    $this->logger->critical(new BusinessMessage($e->getMessage()));
                    $this->logger->critical($e->getPrevious()->getTraceAsString());
                    $this->flashMessages->addFlashMessage(new FlashMessage($e->getMessage()));
                    break;
                default:
                    $response = $response->withStatus(500);
                    $body['data'] = Response::errorData(Response::TYPE_FATAL_ERROR, $e->getMessage());
                    $this->logger->critical($e->getMessage() . \PHP_EOL . $e->getTraceAsString());
                    break;
            }

            if (true === $this->debug) {
                $body['debug']['file'] = $e->getFile();
                $body['debug']['line'] = $e->getLine();
                $body['debug']['stacktrace'] = \explode(\PHP_EOL, $e->getTraceAsString());
            }

            $response = $response->withHeader('Content-type', 'application/json;charset=utf-8');
            $response->getBody()->write(\json_encode($body, \JSON_PRETTY_PRINT));
        }

        return $response;
    }
}
