<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\Menu;

use ExEss\Bundle\CmsBundle\Entity\Menu;
use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
use ExEss\Bundle\CmsBundle\Service\MenuService;
use Symfony\Component\Routing\Annotation\Route;

class ChildrenController
{
    private MenuService $menuService;

    public function __construct(
        MenuService $menuService
    ) {
        $this->menuService = $menuService;
    }

    /**
     * @Route("/menu/{name}", methods={"GET"})
     */
    public function __invoke(Menu $menu): SuccessResponse
    {
        return new SuccessResponse($this->menuService->getSubMenu($menu));
    }
}
