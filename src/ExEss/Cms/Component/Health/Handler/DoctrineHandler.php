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
        try {
            $this->connection->executeQuery("SELECT 1");
            return new HealthCheckResult();
        } catch (\Throwable $e) {
            return new HealthCheckResult(false, 'Database server not available');
        }
    }
}
