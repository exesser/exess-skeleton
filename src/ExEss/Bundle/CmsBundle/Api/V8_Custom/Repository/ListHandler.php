<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Bundle\CmsBundle\Base\Response\BaseListResponse;
use ExEss\Bundle\CmsBundle\Exception\ExternalListFetchException;

class ListHandler
{
    /**
     * @var RepositoryInterface[]
     */
    private array $handlers;

    private FlashMessageContainer $flashMessageContainer;

    /**
     * @param RepositoryInterface[] $handlers
     */
    public function __construct(
        iterable $handlers,
        FlashMessageContainer $flashMessageContainer
    ) {
        $this->handlers = $handlers instanceof \Traversable ? \iterator_to_array($handlers): $handlers;
        $this->flashMessageContainer = $flashMessageContainer;
    }

    public function getList(string $name, array $data, int $page = 1, int $limit = -1): array
    {
        try {
            $handler = $this->handlers[$name] ?? null;
            if (!$handler instanceof RepositoryInterface) {
                throw new \RuntimeException("Could not load handler $name from container");
            }

            $data['params']['page'] = $page;
            $data['params']['limit'] = $limit;

            $response = $handler->findBy($data['params']);

            if (!$response instanceof BaseListResponse) {
                throw new \LogicException("An incorrect response type was given, expected " . BaseListResponse::class);
            }

            if (false === $response->getResult()) {
                $this->flashMessageContainer->addFlashMessage(new FlashMessage($response->getMessage()));
            }

            return $response->getResponse();
        } catch (\Exception $e) {
            throw new ExternalListFetchException($e->getMessage(), 0, $e);
        }
    }
}
