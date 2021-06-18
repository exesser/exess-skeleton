<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Health;

use ExEss\Cms\Component\Health\Model\HealthCheckResult;
use ExEss\Cms\Component\Health\Handler\HealthCheckInterface;

/**
 * Make sure to define the HealthCheckResultNormalizer as a normalizer when using this service
 * @see \ExEss\Cms\Component\Health\Serializer\Normalizer\HealthCheckResultNormalizer
 */
class HealthCheckService
{
    /**
     * @var HealthCheckInterface[]|iterable
     */
    private iterable $handlers;

    private string $defaultSocketTimeout;

    public function __construct(iterable $handlers)
    {
        $this->handlers = $handlers;

        // Set socket timeout to 15 seconds (by default: 60)
        // We don't want to wait too much time to figure out that something is wrong
        // SOAP timeouts cannot be configured by injecting some configuration to SoapClient, so we are
        // force to define it globally here to restrict its scope to health checks only.
        $this->defaultSocketTimeout = \ini_get('default_socket_timeout');
        \ini_set('default_socket_timeout', '15');
    }

    public function __destruct()
    {
        \ini_set('default_socket_timeout', $this->defaultSocketTimeout);
    }

    /**
     * @return array|HealthCheckResult[]
     */
    public function getResult(): array
    {
        $results = [];

        /**
         * @var string $name
         * @var HealthCheckInterface $handler
         */
        foreach ($this->handlers as $name => $handler) {
            try {
                $results[$name] = $handler->getHealthCheck();
            } catch (\Throwable $e) {
                $results[$name] = new HealthCheckResult(false, $e->getMessage());
            }
        }

        return $results;
    }
}
