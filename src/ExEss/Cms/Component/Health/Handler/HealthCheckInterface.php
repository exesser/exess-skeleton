<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Health\Handler;

use ExEss\Cms\Component\Health\Model\HealthCheckResult;

interface HealthCheckInterface
{
    public function getHealthCheck(): HealthCheckResult;
}
