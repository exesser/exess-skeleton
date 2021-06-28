<?php

namespace ExEss\Cms\Component\Flow\Action\BackendCommand;

use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Cms\Cache\CacheAdapterFactory;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Component\Flow\Response\Model;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class ClearCacheCommand implements BackendCommand
{
    private CacheAdapterFactory $cacheAdapterFactory;

    private FlashMessageContainer $flashMessageContainer;

    public function __construct(CacheAdapterFactory $cacheAdapterFactory, FlashMessageContainer $flashMessageContainer)
    {
        $this->cacheAdapterFactory = $cacheAdapterFactory;
        $this->flashMessageContainer = $flashMessageContainer;
    }

    public function execute(array $recordIds, ?Model $model = null): void
    {
        $types = '';
        if ($model instanceof Model) {
            $types = $model->getFieldValue(Dwp::CACHE_KEY);
        }

        if (empty($types)) {
            $this->flashMessageContainer->addFlashMessage(
                new FlashMessage('Please pass a type when clearing the cache', FlashMessage::TYPE_ERROR)
            );
            return;
        }

        if (\is_string($types)) {
            $types = [$types];
        }

        foreach ($types as $type) {
            $cache = $this->cacheAdapterFactory->create($type);

            if ($cache instanceof AdapterInterface) {
                $cache->clear();

                $this->flashMessageContainer->addFlashMessage(
                    new FlashMessage('Cache of type ' . $type . ' have been cleared', FlashMessage::TYPE_SUCCESS)
                );
            } else {
                $this->flashMessageContainer->addFlashMessage(
                    new FlashMessage('Can\'t clear cache of type ' . $type, FlashMessage::TYPE_ERROR)
                );
            }
        }
    }
}
