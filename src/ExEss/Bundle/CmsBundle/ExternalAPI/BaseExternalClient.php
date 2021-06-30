<?php

namespace ExEss\Bundle\CmsBundle\ExternalAPI;

use ExEss\Bundle\CmsBundle\Component\Client\Client;
use ExEss\Bundle\CmsBundle\Component\Client\Request\GuzzleRequest;
use ExEss\Bundle\CmsBundle\Doctrine\Type\HttpMethod;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;

abstract class BaseExternalClient
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array|string|null $data
     * @throws \Exception When the request fails.
     */
    public function request(
        string $uri,
        $data = null,
        string $method = HttpMethod::GET,
        array $headers = []
    ): ?\stdClass {
        $response = $this->client->send(
            new GuzzleRequest(
                $method,
                $uri,
                $headers,
                \is_array($data) || $data instanceof \JsonSerializable ? \json_encode($data) : $data
            )
        );

        return DataCleaner::jsonDecode($response->getData(), false);
    }
}
