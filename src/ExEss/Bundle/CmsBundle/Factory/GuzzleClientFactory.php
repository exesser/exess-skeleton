<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Factory;

use GuzzleHttp\Client;

final class GuzzleClientFactory
{
    public static function create(array $config): Client
    {
        if (!isset($config['base_uri'])) {
            throw new \InvalidArgumentException('Base uri in configuration is not set.');
        }

        if (isset($config['trim_base_uri'])) {
            $config['base_uri'] = (\rtrim($config['base_uri'], '/') . '/');
            unset($config['trim_base_uri']);
        }

        if (isset($config['tcp_keep_alive'])) {
            $config['curl'] = [
                \CURLOPT_TCP_KEEPALIVE => $config['tcp_keep_alive'],
                \CURLOPT_TCP_KEEPIDLE => $config['tcp_keep_alive'],
            ];
            unset($config['tcp_keep_alive']);
        }

        if (isset($config['user'], $config['password'])) {
            $config['auth'] = [$config['user'], $config['password']];
        }

        if (isset($config['proxy_host'], $config['proxy_port'])) {
            $config['proxy'] = \sprintf('%s:%s', $config['proxy_host'], $config['proxy_port']);
            unset($config['proxy_host']);
        }

        if (isset($config['x_api_key'])) {
            $config['headers'] = [
                'x-api-key' => $config['x_api_key']
            ];
            unset($config['x_api_key']);
        }

        // allows to bypass ssl options for testing
        if (isset($config['stream_context'])) {
            $config['stream_context'] = \stream_context_create($config['stream_context']);
        }

        return new Client($config);
    }
}
