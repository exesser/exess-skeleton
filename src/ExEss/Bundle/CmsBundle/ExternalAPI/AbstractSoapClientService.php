<?php

namespace ExEss\Bundle\CmsBundle\ExternalAPI;

use InvalidArgumentException;
use ExEss\Bundle\CmsBundle\Component\Client\Client;
use Psr\Cache\CacheItemPoolInterface;
use ExEss\Bundle\CmsBundle\Soap\AbstractSoapClientBase;

abstract class AbstractSoapClientService
{
    private ?CacheItemPoolInterface $cache;

    protected Client $client;

    private array $config;

    public function __construct(array $config, Client $client, ?CacheItemPoolInterface $cache = null)
    {
        $this->config = $config;
        $this->client = $client;
        $this->cache = $cache;
    }

    /**
     * @see SoapServicesClientPass::process
     */
    public static function getClientOptions(array $config): array
    {
        if (!isset($config['wsdl'])) {
            throw new InvalidArgumentException("Missing config option 'wsdl'");
        }

        $options = [
            AbstractSoapClientBase::WSDL_FEATURES => 1,
        ];

        if (isset($config['proxy_host'], $config['proxy_port'])) {
            $options[AbstractSoapClientBase::WSDL_PROXY_HOST] = $config['proxy_host'];
            $options[AbstractSoapClientBase::WSDL_PROXY_PORT] = $config['proxy_port'];
        }

        // allows to bypass ssl options for testing
        if (isset($config['stream_context'])) {
            $options[AbstractSoapClientBase::WSDL_STREAM_CONTEXT] = \stream_context_create($config['stream_context']);
        }
        if (isset($config['connection_timeout']) && (int)$config['connection_timeout'] > 0) {
            $options[AbstractSoapClientBase::WSDL_CONNECTION_TIMEOUT] = (int)$config['connection_timeout'];
        }

        $options[AbstractSoapClientBase::WSDL_URL] = $config['wsdl'];
        $options[AbstractSoapClientBase::WSDL_CLASSMAP] =
            ($config['overwrite_classmap'] ?? []) + \call_user_func($config['classmap'] . '::get');
        $options[AbstractSoapClientBase::WSDL_EXCEPTIONS] = true;

        return $options;
    }

    protected function isCacheEnabled(): bool
    {
        return $this->cache && !($this->config['cache_disable'] ?? false);
    }

    protected function getCache(): CacheItemPoolInterface
    {
        return $this->cache;
    }
}
