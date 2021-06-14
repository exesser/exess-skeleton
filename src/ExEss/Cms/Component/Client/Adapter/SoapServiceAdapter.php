<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Client\Adapter;

use ExEss\Cms\Component\Client\ClientConfig;
use ExEss\Cms\Component\Client\Request\RequestInterface;
use ExEss\Cms\Component\Client\Response\Response;
use ExEss\Cms\Component\Client\Response\ResponseInterface;
use ExEss\Cms\Component\Client\Exception\ClientRequestException;
use ExEss\Cms\Component\Client\Request\SoapRequest;
use Psr\Log\LoggerInterface;
use ExEss\Cms\Soap\AbstractSoapClientBase;

class SoapServiceAdapter implements ClientAdapterInterface
{
    private \SoapClient $client;

    private string $wsdl;

    private LoggerInterface $clientRequestLogger;

    public function __construct(AbstractSoapClientBase $service, string $wsdl, LoggerInterface $clientRequestLogger)
    {
        $this->client = $service;
        $this->wsdl = $wsdl;
        $this->clientRequestLogger = $clientRequestLogger;
    }

    /**
     * @param SoapRequest $request
     */
    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        $response = $this->client->{$request->getFunction()}($request->getData());

        try {
            $this->clientRequestLogger->info(\sprintf(
                "Real payload for call to %s: %s",
                $request->getPath(),
                $this->client->getSoapClient()->__getLastRequest()
            ));
        } catch (\Throwable $e) {
            $this->clientRequestLogger->error(\sprintf(
                "Error getting real payload for call to %s: %s",
                $request->getPath(),
                $e->getMessage()
            ));
        }

        if ($response === false) {
            $e = $this->client->getLastErrorForMethod(\get_class($this->client) . '::' . $request->getFunction())
                ?? null;
            throw new ClientRequestException($e instanceof \SoapFault ? $e->getMessage() : "");
        }

        $headers = $this->client->getLastResponseHeaders();
        if (\is_string($headers)) {
            $headers = $this->headersToArray($headers);
        }

        if (!\is_array($headers)) {
            $headers = [];
        }

        return new Response(200, $headers, $response);
    }

    public function getClientConfig(): ClientConfig
    {
        return (new ClientConfig($this->wsdl));
    }

    private function headersToArray(?string $headers): array
    {
        if ($headers === null) {
            return [];
        }

        $headersArray = [];
        foreach (\explode("\r\n", $headers) as $line) {
            if (\strpos($line, ':')) {
                $headerParts = \explode(':', $line);
                $headersArray[$headerParts[0]] = \trim(\implode(':', \array_slice($headerParts, 1)));
            }
        }

        return $headersArray;
    }

    public function getClient(): AbstractSoapClientBase
    {
        return $this->client;
    }
}
