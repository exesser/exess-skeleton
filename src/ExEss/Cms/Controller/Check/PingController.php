<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Check;

use ExEss\Cms\Component\Health\PingService;
use ExEss\Cms\Http\SuccessResponse;
use Symfony\Component\Routing\Annotation\Route;

class PingController
{
    private PingService $pingService;

    public function __construct(PingService $pingService)
    {
        $this->pingService = $pingService;
    }

    /**
     * @Route("/Api/check/ping", methods={"GET"})
     */
    public function __invoke(): SuccessResponse
    {
        return new SuccessResponse($this->pingService->getResult());
    }
}
