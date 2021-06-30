<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Adapter\Transport;

use ExEss\Bundle\CmsBundle\Adapter\Exception\TransportException;
use ExEss\Bundle\CmsBundle\Adapter\Request\Request;
use ExEss\Bundle\CmsBundle\Adapter\Response\Response;
use GuzzleHttp\Exception\GuzzleException;
use ExEss\Bundle\CmsBundle\Component\Client\Client;
use ExEss\Bundle\CmsBundle\Component\Client\Request\GuzzleRequest;

class GuzzleTransport implements Transport
{
    private Client $guzzle;

    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function request(Request $request, ?\Closure $responseHandler = null): Response
    {
        try {
            $data = $request->getParameters();

            $guzzleRequest = new GuzzleRequest(
                $request->getMethod(),
                $request->getUri(),
                $request->getHeaders(),
                \is_array($data) || $data instanceof \JsonSerializable ? \json_encode($data) : $data
            );

            $response = $this->guzzle->send($guzzleRequest);
        } catch (GuzzleException $e) {
            throw new TransportException($e->getMessage(), $e);
        }

        if (!$responseHandler) {
            throw new TransportException('Transport has no implementation to handle the response');
        }

        return $responseHandler(
            $response->getStatusCode(),
            (string) $response->getData(),
            $response->getHeaders()
        );
    }
}
