<?php

namespace ExEss\Cms\Http\EventSubscriber;

use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Cms\Exception\EmailForExportNotFoundException;
use ExEss\Cms\Exception\ExternalListFetchException;
use ExEss\Cms\Exception\NotAllowedException;
use ExEss\Cms\Exception\NotAuthenticatedException;
use ExEss\Cms\Exception\NotAuthorizedException;
use ExEss\Cms\Exception\NotFoundException;
use ExEss\Cms\FLW_Flows\Event\Exception\CommandException;
use ExEss\Cms\Http\ErrorResponse;
use ExEss\Cms\Logger\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ExceptionSubscriber implements EventSubscriberInterface
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
        $this->debug = $debug;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::EXCEPTION => [
                ['processException', 10],
                ['logException', 0],
                ['notifyException', -10],
                ['debugException', -20],
            ],
        ];
    }

    public function processException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        switch (true) {
            case $e instanceof AccessDeniedException:
            case $e instanceof NotAuthenticatedException:
                $response = new ErrorResponse(Response::HTTP_UNAUTHORIZED);
                break;
            case $e instanceof NotAuthorizedException:
                $response = new ErrorResponse(Response::HTTP_FORBIDDEN);
                break;
            case $e instanceof NotAllowedException:
                $response = new ErrorResponse(
                    Response::HTTP_METHOD_NOT_ALLOWED,
                    ErrorResponse::errorData(ErrorResponse::TYPE_NOT_ALLOWED_EXCEPTION, $e->getMessage())
                );
                break;
            case $e instanceof NotFoundException:
                $response = new ErrorResponse(
                    Response::HTTP_NOT_FOUND,
                    ErrorResponse::errorData(ErrorResponse::TYPE_NOT_FOUND_EXCEPTION, $e->getMessage())
                );
                break;
            case $e instanceof CommandException:
            case $e instanceof EmailForExportNotFoundException:
            case $e instanceof \LogicException:
            case $e instanceof \DomainException:
                $response = new ErrorResponse(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    ErrorResponse::errorData(ErrorResponse::TYPE_DOMAIN_EXCEPTION, $e->getMessage())
                );
                break;
            default:
                $response = new ErrorResponse(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ErrorResponse::errorData(ErrorResponse::TYPE_FATAL_ERROR, $e->getMessage())
                );
                break;
        }

        $event->setResponse($response);
    }

    public function logException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        switch (true) {
            case $e instanceof NotAllowedException:
                $this->logger->warning($e->getMessage() . \PHP_EOL . $e->getTraceAsString());
                break;
            case $e instanceof CommandException:
            case $e instanceof EmailForExportNotFoundException:
            case $e instanceof \LogicException:
            case $e instanceof \DomainException:
                $this->logger->error($e->getMessage() . \PHP_EOL . $e->getTraceAsString());
                break;
            default:
                $this->logger->critical($e->getMessage() . \PHP_EOL . $e->getTraceAsString());
                break;
        }
    }

    public function notifyException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        switch (true) {
            case $e instanceof NotAuthorizedException:
                $this->flashMessages->addFlashMessage(new FlashMessage(
                    "You don't have the rights to perform this action"
                ));
                break;
            case $e instanceof CommandException:
            case $e instanceof EmailForExportNotFoundException:
            case $e instanceof ExternalListFetchException:
                $this->flashMessages->addFlashMessage(new FlashMessage($e->getMessage()));
                break;
            default:
                break;
        }
    }

    public function debugException(ExceptionEvent $event): void
    {
        if (true === $this->debug) {
            $e = $event->getThrowable();

            /** @var ErrorResponse $response */
            $response = $event->getResponse();

            $response->addDebugInformation($e);
        }
    }
}
