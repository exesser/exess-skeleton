<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Check;

use ExEss\Cms\Component\Health\HealthCheckService;
use ExEss\Cms\Http\SuccessResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthController
{
    private HealthCheckService $healthCheckService;

    public function __construct(HealthCheckService $healthCheckService)
    {
        $this->healthCheckService = $healthCheckService;
    }

    /**
     * @Route("/Api/check/health", methods={"GET"})
     */
    public function __invoke(): SuccessResponse
    {
        return new SuccessResponse(['results' => $this->healthCheckService->getResult()]);
    }
}
