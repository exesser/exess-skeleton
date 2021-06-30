<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Service;

use ExEss\Bundle\CmsBundle\Doctrine\Type\TranslationDomain;
use ExEss\Bundle\CmsBundle\Entity\Menu;
use ExEss\Bundle\CmsBundle\Repository\DashboardRepository;
use ExEss\Bundle\CmsBundle\Repository\MenuRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuService
{
    private MenuRepository $menuRepository;

    private TranslatorInterface $translator;

    private DashboardRepository $dashboardRepository;

    public function __construct(
        MenuRepository $menuRepository,
        DashboardRepository $dashboardRepository,
        TranslatorInterface $translator
    ) {
        $this->menuRepository = $menuRepository;
        $this->translator = $translator;
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getMainMenu(): array
    {
        $menus = [];
        foreach ($this->menuRepository->getMenus() as $menu) {
            $menus[] = [
                'name' => $this->translator->trans($menu->getName(), [], TranslationDomain::MAIN_MENU),
                'link' => $menu->getLink(),
                'params' => $menu->getParams(),
                'icon' => $menu->getIcon(),
            ];
        }

        return $menus;
    }

    public function getSubMenu(Menu $menu): array
    {
        $return = [];
        foreach ($this->dashboardRepository->getFor($menu) as $dashboard) {
            $return[] = [
                'label' => $this->translator->trans($dashboard->getName(), [], TranslationDomain::SUB_MENU),
                'link' => 'dashboard',
                'params' => [
                    'dashboardId' => $dashboard->getKey(),
                    'mainMenuKey' => $menu->getName(),
                    'recordId' => null,
                ],
            ];
        }

        return $return;
    }
}
