<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Client\Adapter;

use GuzzleHttp\ClientInterface;
use ExEss\Cms\Component\Client\ClientConfig;
use ExEss\Cms\Component\Client\Request\RequestInterface;
use ExEss\Cms\Component\Client\Response\ResponseInterface;

interface ClientAdapterInterface
{
    public function send(RequestInterface $request, array $options): ResponseInterface;

    public function getClientConfig(): ClientConfig;

    /**
     * @return mixed|SoapClientInterface|ClientInterface
     */
    public function getClient();
}
