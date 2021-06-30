<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Health\Handler;

use ExEss\Bundle\CmsBundle\Component\Health\Model\HealthCheckResult;

interface HealthCheckInterface
{
    public function getHealthCheck(): HealthCheckResult;
}
