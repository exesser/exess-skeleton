<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use ExEss\Cms\Service\MenuService;
use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Params\GetSubMenuParams;

class MenuController extends AbstractApiController
{
    private MenuService $menuService;

    public function __construct(
        MenuService $menuService
    ) {
        $this->menuService = $menuService;
    }

    public function getMainMenu(Request $request, Response $response, array $args): Response
    {
        return $this->generateResponse(
            $response,
            200,
            $this->menuService->getMainMenu()
        );
    }

    public function getSubmenu(Request $request, Response $response, array $args, GetSubMenuParams $params): Response
    {
        return $this->generateResponse(
            $response,
            200,
            $this->menuService->getSubMenu($params->getMainMenuKey())
        );
    }
}
