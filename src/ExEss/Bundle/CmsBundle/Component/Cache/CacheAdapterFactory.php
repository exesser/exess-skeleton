<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Cache;

use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class CacheAdapterFactory
{
    private const NAMESPACE_PREFIX = 'exess_cms_cache';

    private LoggerInterface $logger;

    private bool $disabled;

    private string $host;

    private int $port;

    public function __construct(LoggerInterface $logger, bool $disabled, string $host, int $port)
    {
        $this->disabled = $disabled;
        $this->host = $host;
        $this->port = $port;
        $this->logger = $logger;
    }

    public function create(
        string $namespace = Dictionary::DEFAULT,
        int $lifetime = Dictionary::TTL_DEFAULT
    ): AdapterInterface {
        if ($this->disabled) {
            return new NullAdapter();
        }

        $redis = new \Redis();
        $redis->connect($this->host, $this->port);

        $adapter = new RedisAdapter($redis, self::NAMESPACE_PREFIX . '.' . $namespace, $lifetime);
        $adapter->setLogger($this->logger);

        return $adapter;
    }
}
