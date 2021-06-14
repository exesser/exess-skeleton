<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Health;

use ExEss\Cms\Component\Health\Model\HealthCheckResult;
use ExEss\Cms\Component\Health\Serializer\Normalizer\HealthCheckResultNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use ExEss\Cms\Component\Health\Handler\HealthCheckInterface;

/**
 * Make sure to define the HealthCheckResultNormalizer as a normalizer when using this service
 * @see \ExEss\Cms\Component\Health\Serializer\Normalizer\HealthCheckResultNormalizer
 */
class HealthService
{
    /**
     * @var HealthCheckInterface[]|iterable
     */
    private iterable $handlers;

    private SerializerInterface $serializer;

    private string $defaultSocketTimeout;

    public function __construct(iterable $handlers, SerializerInterface $serializer)
    {
        $this->handlers = $handlers;
        $this->serializer = $serializer;

        $this->defaultSocketTimeout = \ini_get('default_socket_timeout');

        // Set socket timeout to 15 seconds (by default: 60)
        // We don't want to wait too much time to figure out that something is wrong
        // SOAP timeouts cannot be configured by injecting some configuration to SoapClient, so we are
        // force to define it globally here to restrict its scope to health checks only.
        \ini_set('default_socket_timeout', '15');
    }

    public function __destruct()
    {
        \ini_set('default_socket_timeout', $this->defaultSocketTimeout);
    }

    public function getResult(): string
    {
        $results = [];

        /**
         * @var string $name
         * @var HealthCheckInterface $handler
         */
        foreach ($this->handlers as $name => $handler) {
            try {
                $results[$name] = $handler->getHealthCheck();
            } catch (\Exception $e) {
                $results[$name] = new HealthCheckResult(false, $e->getMessage());
            }
        }

        return $this
            ->serializer
            ->normalize($results, 'array', [HealthCheckResultNormalizer::HEALTH_CHECK => true])
            ->asXml();
    }
}
