<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Http\EventSubscriber;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Bundle\CmsBundle\Exception\ExternalListFetchException;
use ExEss\Bundle\CmsBundle\Exception\NotAllowedException;
use ExEss\Bundle\CmsBundle\Exception\NotAuthenticatedException;
use ExEss\Bundle\CmsBundle\Exception\NotAuthorizedException;
use ExEss\Bundle\CmsBundle\Exception\NotFoundException;
use ExEss\Bundle\CmsBundle\Component\Flow\Event\Exception\CommandException;
use ExEss\Bundle\CmsBundle\Http\ErrorResponse;
use ExEss\Bundle\CmsBundle\Logger\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
        $event->setResponse(
            $this->transformToResponse($event->getThrowable())
        );
    }

    public function transformToResponse(\Throwable $e): Response
    {
        switch (true) {
            case $e instanceof HttpException:
                return new ErrorResponse($e->getStatusCode());
            case $e instanceof AccessDeniedException:
            case $e instanceof NotAuthenticatedException:
                return new ErrorResponse(Response::HTTP_UNAUTHORIZED);
            case $e instanceof NotAuthorizedException:
                return new ErrorResponse(Response::HTTP_FORBIDDEN);
            case $e instanceof NotAllowedException:
                return new ErrorResponse(
                    Response::HTTP_METHOD_NOT_ALLOWED,
                    ErrorResponse::errorData(ErrorResponse::TYPE_NOT_ALLOWED_EXCEPTION, $e->getMessage())
                );
            case $e instanceof NotFoundException:
                return new ErrorResponse(
                    Response::HTTP_NOT_FOUND,
                    ErrorResponse::errorData(ErrorResponse::TYPE_NOT_FOUND_EXCEPTION, $e->getMessage())
                );
            case $e instanceof CommandException:
            case $e instanceof \LogicException:
            case $e instanceof \DomainException:
                return new ErrorResponse(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    ErrorResponse::errorData(ErrorResponse::TYPE_DOMAIN_EXCEPTION, $e->getMessage())
                );
            default:
                return new ErrorResponse(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ErrorResponse::errorData(ErrorResponse::TYPE_FATAL_ERROR, $e->getMessage())
                );
        }
    }

    public function logException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        switch (true) {
            case $e instanceof NotAllowedException:
                $this->logger->warning($e->getMessage() . \PHP_EOL . $e->getTraceAsString());
                break;
            case $e instanceof CommandException:
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
