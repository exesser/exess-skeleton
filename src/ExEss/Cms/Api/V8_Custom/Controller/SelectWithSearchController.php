<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Params\SelectWithSearchParams;
use ExEss\Cms\FESelectWithSearch\SelectWithSearchService;

class SelectWithSearchController extends AbstractApiController
{
    private SelectWithSearchService $selectWithSearchService;

    public function __construct(
        SelectWithSearchService $selectWithSearchService
    ) {
        $this->selectWithSearchService = $selectWithSearchService;
    }

    public function getSelectOptions(
        Request $request,
        Response $response,
        array $args,
        SelectWithSearchParams $params
    ): Response {
        $selectOptions = $this->selectWithSearchService->getSelectOptions(
            $params->getSelectWithSearchName(),
            $params->toArray()
        );

        return $this->generateResponse($response, 200, $selectOptions);
    }
}
