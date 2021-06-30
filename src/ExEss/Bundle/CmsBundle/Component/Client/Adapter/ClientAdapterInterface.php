<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Client\Adapter;

use GuzzleHttp\ClientInterface;
use ExEss\Bundle\CmsBundle\Component\Client\ClientConfig;
use ExEss\Bundle\CmsBundle\Component\Client\Request\RequestInterface;
use ExEss\Bundle\CmsBundle\Component\Client\Response\ResponseInterface;

interface ClientAdapterInterface
{
    public function send(RequestInterface $request, array $options): ResponseInterface;

    public function getClientConfig(): ClientConfig;

    /**
     * @return mixed|SoapClientInterface|ClientInterface
     */
    public function getClient();
}
