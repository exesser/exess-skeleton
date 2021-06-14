<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Params\SidebarParams;
use ExEss\Cms\Api\V8_Custom\Sidebar\SidebarFactory;

class SidebarController extends AbstractApiController
{
    private SidebarFactory $sidebarFactory;

    public function __construct(
        SidebarFactory $sidebarFactory
    ) {
        $this->sidebarFactory = $sidebarFactory;
    }

    public function getSidebar(Request $req, Response $res, array $args, SidebarParams $params): Response
    {
        $response = $this->sidebarFactory->createSidebar($params)->getData();

        if (!$response) {
            return $this->generateResponse(
                $res,
                404,
                [
                    'type' => \ExEss\Cms\Dictionary\Response::TYPE_NOT_FOUND_EXCEPTION,
                    'message' => 'No ' . $params->getObject() .  ' found for ID ' . $params->getId()
                ]
            );
        }

        return $this->generateResponse($res, 200, $response);
    }
}
