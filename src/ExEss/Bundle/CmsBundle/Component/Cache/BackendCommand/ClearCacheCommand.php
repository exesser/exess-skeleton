<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Cache\BackendCommand;

use ExEss\Bundle\CmsBundle\Component\Cache\CacheAdapterFactory;
use ExEss\Bundle\CmsBundle\Component\Core\Flow\Action\BackendCommandInterface;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Component\Flow\Response\Model;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class ClearCacheCommand implements BackendCommandInterface
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
            throw new \InvalidArgumentException('Please pass a type when clearing the cache');
        }

        if (\is_string($types)) {
            $types = [$types];
        }

        foreach ($types as $type) {
            $cache = $this->cacheAdapterFactory->create($type);

            if (!$cache instanceof AdapterInterface) {
                throw new \InvalidArgumentException("Can't clear cache of type $type");
            }

            $cache->clear();

            $this->flashMessageContainer->addFlashMessage(
                new FlashMessage("Cache of type $type has been cleared", FlashMessage::TYPE_SUCCESS)
            );
        }
    }
}
