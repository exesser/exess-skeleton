<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\Dashboard;

use ExEss\Bundle\CmsBundle\Entity\Dashboard;
use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
use ExEss\Bundle\CmsBundle\Service\DashboardService;
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
