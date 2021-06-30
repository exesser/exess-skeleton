<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Client\Adapter;

use ExEss\Bundle\CmsBundle\Component\Client\ClientConfig;
use ExEss\Bundle\CmsBundle\Component\Client\Request\RequestInterface;
use ExEss\Bundle\CmsBundle\Component\Client\Response\Response;
use ExEss\Bundle\CmsBundle\Component\Client\Response\ResponseInterface;
use ExEss\Bundle\CmsBundle\Component\Client\Exception\ClientRequestException;
use ExEss\Bundle\CmsBundle\Component\Client\Request\SoapRequest;
use Symfony\Component\Serializer\SerializerInterface;

class SoapClientAdapter implements ClientAdapterInterface
{
    private \SoapClient $client;

    private string $wsdl;

    private SerializerInterface $serializer;

    public function __construct(\SoapClient $client, string $wsdl, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->wsdl = $wsdl;
        $this->serializer = $serializer;
    }

    /**
     * @param SoapRequest $request
     */
    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        try {
            $response = $this->client->__soapCall(
                $request->getFunction(),
                [new \SoapVar($request->getData(), \XSD_ANYXML)],
                $options
            );
        } catch (\SoapFault $e) {
            throw new ClientRequestException($e->getMessage());
        }

        return $this->serializer->denormalize(
            $response,
            Response::class,
            \stdClass::class
        );
    }

    public function getClientConfig(): ClientConfig
    {
        return (new ClientConfig($this->wsdl));
    }

    public function getClient(): \SoapClient
    {
        return $this->client;
    }
}
