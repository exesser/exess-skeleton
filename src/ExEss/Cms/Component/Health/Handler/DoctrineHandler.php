<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Health\Handler;

use Doctrine\DBAL\Connection;
use ExEss\Cms\Component\Health\Model\HealthCheckResult;

class DoctrineHandler implements HealthCheckInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getHealthCheck(): HealthCheckResult
    {
        if ($this->connection->ping() === false) {
            return new HealthCheckResult(false, 'Database server not available');
        }

        return new HealthCheckResult();
    }
}
