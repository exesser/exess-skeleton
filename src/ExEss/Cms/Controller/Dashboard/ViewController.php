<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Dashboard;

use ExEss\Cms\Entity\Dashboard;
use ExEss\Cms\Http\SuccessResponse;
use ExEss\Cms\Service\DashboardService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ViewController
{
    private DashboardService $dashboardService;

    public function __construct(
        DashboardService $dashboardService
    ) {
        $this->dashboardService = $dashboardService;
    }

    /**
     * @Route("/Api/dashboard/{key}/{recordId}", methods={"GET"})
     */
    public function __invoke(Request $request, Dashboard $dashboard, ?string $recordId = null): SuccessResponse
    {
        return new SuccessResponse(
            $this->dashboardService->getDashboard(
                $dashboard,
                $request->query->getIterator()->getArrayCopy(),
                $recordId
            )
        );
    }
}
