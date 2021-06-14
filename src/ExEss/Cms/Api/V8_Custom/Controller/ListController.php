<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use ExEss\Cms\ListFunctions\ListExportService;
use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Params\ListParams;
use ExEss\Cms\ListFunctions\ListFunctions;

class ListController extends AbstractApiController
{
    private ListFunctions $listHandler;

    private ListExportService $exportService;

    public function __construct(
        ListFunctions $listHandler,
        ListExportService $exportService
    ) {
        $this->listHandler = $listHandler;
        $this->exportService = $exportService;
    }

    public function getList(Request $request, Response $response, array $args, ListParams $params): Response
    {
        return $this->generateResponse(
            $response,
            200,
            $this->listHandler->getList($params)
        );
    }

    public function getListCSV(Request $request, Response $response, array $args, ListParams $params): Response
    {
        $link = $this->exportService->export(
            $this->listHandler->getList($params)
        );

        if (null === $link) {
            // No link is returned when the CSV file is not created, but instead put on a queue.
            // Do download prompt should happen.
            return $this->generateResponse($response, 200);
        }

        return $this->generateResponse(
            $response,
            200,
            [
                'command' => 'openLink',
                'arguments' => [
                    'link' => $link
                ]
            ]
        );
    }
}
