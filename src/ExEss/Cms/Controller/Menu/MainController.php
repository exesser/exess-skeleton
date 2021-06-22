<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Menu;

use ExEss\Cms\Http\SuccessResponse;
use ExEss\Cms\Service\MenuService;
use Symfony\Component\Routing\Annotation\Route;

class MainController
{
    private MenuService $menuService;

    public function __construct(
        MenuService $menuService
    ) {
        $this->menuService = $menuService;
    }

    /**
     * @Route("/Api/menu", methods={"GET"})
     */
    public function __invoke(): SuccessResponse
    {
        return new SuccessResponse($this->menuService->getMainMenu());
    }
}
