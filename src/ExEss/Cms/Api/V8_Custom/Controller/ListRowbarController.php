<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Params\ListRowbarParams;

class ListRowbarController extends AbstractApiController
{
    public function getListRowBar(
        Request $request,
        Response $response,
        array $args,
        ListRowbarParams $params
    ): Response {
        return $this->generateResponse($response, 200, $params);
    }
}
