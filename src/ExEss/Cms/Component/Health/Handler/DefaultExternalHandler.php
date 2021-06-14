<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Health\Handler;

use ExEss\Cms\Component\Client\Client;
use ExEss\Cms\Component\Client\Exception\ClientRequestException;
use ExEss\Cms\Component\Client\Request\GuzzleRequest;
use ExEss\Cms\Component\Health\Model\HealthCheckResult;
use ExEss\Cms\Doctrine\Type\HttpMethod;

/**
 * Handler for external API calls
 */
class DefaultExternalHandler implements HealthCheckInterface
{
    private Client $client;

    private string $pingPath;

    public function __construct(Client $client, string $pingPath)
    {
        $this->client = $client;
        $this->pingPath = $pingPath;
    }

    /**
     * @inheritdoc
     */
    public function getHealthCheck(): HealthCheckResult
    {
        try {
            $response = $this->client->send(new GuzzleRequest(
                HttpMethod::GET,
                $this->pingPath,
                [
                    'Accept' => 'application/xml',
                    'Content-Type' => 'application/xml'
                ]
            ));

            return new HealthCheckResult(
                $response->getStatusCode() === 200,
                $response->getStatusCode() === 200
                    ? 'OK'
                    : 'Action was processed successfully, but status code was ' . $response->getStatusCode()
            );
        } catch (ClientRequestException $e) {
            $message = 'RequestException with no response';

            if ($e->hasResponse()) {
                $message = $e->getResponse()->getStatusCode() . ' / '
                    . $e->getResponse()->getReasonPhrase() . ' @ '
                    . $e->getRequest()->getUri();
            }

            return new HealthCheckResult(false, $message);
        }
    }
}
