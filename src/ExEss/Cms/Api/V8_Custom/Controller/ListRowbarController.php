<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Params\ListRowbarActionParams;
use ExEss\Cms\Api\V8_Custom\Params\ListRowbarParams;
use ExEss\Cms\Api\V8_Custom\Repository\ListRowbarRepository;

class ListRowbarController extends AbstractApiController
{
    private ListRowbarRepository $repository;

    public function __construct(
        ListRowbarRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function getListRowActions(
        Request $request,
        Response $response,
        array $args,
        ListRowbarActionParams $params
    ): Response {
        return $this->generateResponse(
            $response,
            200,
            [
                'buttons' => $this->repository->findListRowActions(
                    $params->getListKey(),
                    $params->getRecordId(),
                    $params->getActionData()
                ),
            ]
        );
    }

    public function getListRowBar(
        Request $request,
        Response $response,
        array $args,
        ListRowbarParams $params
    ): Response {
        return $this->generateResponse($response, 200, $params);
    }
}
