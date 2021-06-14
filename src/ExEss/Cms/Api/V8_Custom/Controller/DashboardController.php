<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use ExEss\Cms\Service\DashboardService;
use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Params\GetDashboardParams;

class DashboardController extends AbstractApiController
{
    private DashboardService $dashboardService;

    public function __construct(
        DashboardService $dashboardService
    ) {
        $this->dashboardService = $dashboardService;
    }

    public function getDashboard(Request $request, Response $res, array $args, GetDashboardParams $params): Response
    {
        return $this->generateResponse(
            $res,
            200,
            $this->dashboardService->getDashboard($params->getDashBoardName(), $params->toArray())
        );
    }
}
