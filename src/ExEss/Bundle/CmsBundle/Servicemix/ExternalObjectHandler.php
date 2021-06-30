<?php
namespace ExEss\Bundle\CmsBundle\Servicemix;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\RepositoryInterface;
use ExEss\Bundle\CmsBundle\Base\Response\BaseResponse;
use ExEss\Bundle\CmsBundle\Exception\NotFoundException;
use ExEss\Bundle\CmsBundle\Logger\Logger;

class ExternalObjectHandler
{
    private array $handlers;

    private FlashMessageContainer $flashMessageContainer;

    private Logger $logger;

    public function __construct(iterable $handlers, FlashMessageContainer $flashMessageContainer, Logger $logger)
    {
        $this->handlers = $handlers instanceof \Traversable ? \iterator_to_array($handlers): $handlers;
        $this->flashMessageContainer = $flashMessageContainer;
        $this->logger = $logger;
    }

    /**
     * @return mixed maybe an object, maybe a BaseResponse, maybe null...
     */
    public function getObject(string $handlerName, array $postedData)
    {
        try {
            $handler = $this->handlers[$handlerName] ?? null;

            if (null === $handler || !$handler instanceof RepositoryInterface) {
                throw new NotFoundException("No handler with name '$handlerName' was registered");
            }

            $response = $handler->findOneBy($postedData);

            if ($response instanceof BaseResponse && false === $response->getResult()) {
                $this->flashMessageContainer->addFlashMessage(new FlashMessage($response->getMessage()));
            }

            return $response;
        } catch (\Throwable $e) {
            $this->logger->critical($e->getMessage());
            $this->flashMessageContainer->addFlashMessage(
                new FlashMessage($e->getMessage())
            );
        }

        return null;
    }

    /**
     * @throws \InvalidArgumentException When the handler is not found.
     */
    public function combineCalls(string $handlerName): bool
    {
        if (!\array_key_exists($handlerName, $this->handlers)) {
            throw new \InvalidArgumentException(
                'Handler ' . $handlerName . ' not set'
            );
        }

        $handler = $this->handlers[$handlerName];

        if (!$handler instanceof RepositoryInterface) {
            return false;
        }

        return $handler->combineCalls();
    }
}
