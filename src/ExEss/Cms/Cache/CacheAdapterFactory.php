<?php
namespace ExEss\Cms\Cache;

use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class CacheAdapterFactory
{
    private const NAMESPACE_PREFIX = 'crm_cache';

    private LoggerInterface $logger;

    private bool $disabled;

    private string $host;

    private string $port;

    public function __construct(LoggerInterface $logger, bool $disabled, string $host, string $port)
    {
        $this->disabled = $disabled;
        $this->host = $host;
        $this->port = $port;
        $this->logger = $logger;
    }

    public function create(string $namespace = Cache::DEFAULT, int $lifetime = Cache::TTL_DEFAULT): AdapterInterface
    {
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
