<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use ExEss\Cms\Service\FilterService;
use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Params\GetFilterParams;

class FilterController extends AbstractApiController
{
    private FilterService $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    public function getFilter(Request $request, Response $response, array $args, GetFilterParams $params): Response
    {
        return $this->generateResponse(
            $response,
            200,
            $this->filterService->getFilters($params->getList())
        );
    }
}
