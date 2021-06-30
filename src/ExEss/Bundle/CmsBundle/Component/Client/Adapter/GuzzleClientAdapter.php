<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Client\Adapter;

use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use ExEss\Bundle\CmsBundle\Component\Client\ClientConfig;
use ExEss\Bundle\CmsBundle\Component\Client\Exception\ClientRequestException;
use ExEss\Bundle\CmsBundle\Component\Client\Request\GuzzleRequest;
use ExEss\Bundle\CmsBundle\Component\Client\Request\RequestInterface;
use ExEss\Bundle\CmsBundle\Component\Client\Response\Response;
use ExEss\Bundle\CmsBundle\Component\Client\Response\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

class GuzzleClientAdapter implements ClientAdapterInterface
{
    private ClientInterface $client;

    private SerializerInterface $serializer;

    public function __construct(ClientInterface $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param GuzzleRequest $request
     */
    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        try {
            $guzzleResponse = $this->client->send($request, $options);
        } catch (RequestException $e) {
            throw new ClientRequestException($e->getMessage());
        }

        /** @var Response $response */
        $response = $this->serializer->denormalize(
            $guzzleResponse,
            Response::class,
            GuzzleResponse::class
        );

        $responseData = $response->getData();

        if (
            ($request->getHeaders()['Accept'][0] ?? null) === GuzzleRequest::CONTENT_TYPE_JSON
            && !empty($responseData)
        ) {
            DataCleaner::jsonDecode($responseData);
        }

        return $response;
    }

    public function getClientConfig(): ClientConfig
    {
        $baseUri = $this->client->getConfig('base_uri');

        return (new ClientConfig((string) $baseUri))
            ->setHost($baseUri->getHost())
            ->setPath($baseUri->getPath())
            ->setQuery($baseUri->getQuery())
            ;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }
}
