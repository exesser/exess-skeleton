<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use ExEss\Cms\Service\CrudService;
use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;

class CrudController extends AbstractApiController
{
    private CrudService $crudService;

    public function __construct(CrudService $crudService)
    {
        $this->crudService = $crudService;
    }

    public function getRecordsInformation(Request $request, Response $response): Response
    {
        return $this->generateResponse(
            $response,
            200,
            $this->crudService->getRecordsInformation()
        );
    }
}
