<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\Check;

use ExEss\Bundle\CmsBundle\Component\Health\HealthCheckService;
use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
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
