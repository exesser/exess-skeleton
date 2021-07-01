<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\Check;

use ExEss\Bundle\CmsBundle\Component\Health\PingService;
use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
use Symfony\Component\Routing\Annotation\Route;

class PingController
{
    private PingService $pingService;

    public function __construct(PingService $pingService)
    {
        $this->pingService = $pingService;
    }

    /**
     * @Route("/check/ping", methods={"GET"})
     */
    public function __invoke(): SuccessResponse
    {
        return new SuccessResponse($this->pingService->getResult());
    }
}
