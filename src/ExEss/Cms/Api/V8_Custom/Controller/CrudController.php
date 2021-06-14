<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\CRUD\CrudService;

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
