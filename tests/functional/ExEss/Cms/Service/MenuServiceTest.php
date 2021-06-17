<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Service;

use ExEss\Cms\Doctrine\Type\TranslationDomain;
use ExEss\Cms\Service\MenuService;
use Helper\Testcase\FunctionalTestCase;
use Symfony\Component\Translation\Translator;

class MenuServiceTest extends FunctionalTestCase
{
    private const DUMMY_LINK_C = 'dummy';

    /**
     * @var \Mockery\Mock|Translator
     */
    private $translator;

    private MenuService $menuService;

    private array $currentRecords;

    public function _before(): void
    {
        $this->translator = \Mockery::mock(Translator::class);
        $this->tester->mockService(Translator::class, $this->translator);

        $this->menuService = $this->tester->grabService(MenuService::class);

        $this->currentRecords = $this->tester->grabAllFromDatabase('menu_mainmenu');
        $this->tester->deleteFromDatabase('menu_mainmenu');
    }

    public function _after(): void
    {
        $this->tester->restoreInDatabase('menu_mainmenu', $this->currentRecords);
    }

    public function testFindMainMenu(): void
    {
        // setup
        $menus = [
            'Sales marketing' => [
                'name' => 'sales-marketing',
                'link_c' => self::DUMMY_LINK_C,
                'params_c' => '{"dashboardId": "sales-marketing", "mainMenuKey": "sales-marketing"}',
                'icon_c' => 'icon-marketing',
                'display_order_c' => 30,
            ],
            'Finance' => [
                'name' => 'finance',
                'link_c' => self::DUMMY_LINK_C,
                'params_c' => '{"dashboardId": "finance", "mainMenuKey": "finance"}',
                'icon_c' => 'icon-finance',
                'display_order_c' => 10,
            ],
            'Security' => [
                'name' => 'security',
                'link_c' => self::DUMMY_LINK_C,
                'params_c' => '{"dashboardId": "security", "mainMenuKey": "security"}',
                'icon_c' => 'icon-security',
                'display_order_c' => 20,
            ]
        ];
        $expected = [
            [
                'name' => "Finance",
                'link' => self::DUMMY_LINK_C,
                'params' => [
                    'dashboardId' => 'finance',
                    'mainMenuKey' => 'finance',
                ],
                'icon' => 'icon-finance'
            ],
            [
                'name' => "Security",
                'link' => self::DUMMY_LINK_C,
                'params' => [
                    'dashboardId' => 'security',
                    'mainMenuKey' => 'security',
                ],
                'icon' => 'icon-security'
            ],
            [
                'name' => "Sales marketing",
                'link' => self::DUMMY_LINK_C,
                'params' => [
                    'dashboardId' => 'sales-marketing',
                    'mainMenuKey' => 'sales-marketing',
                ],
                'icon' => 'icon-marketing'
            ],
        ];

        foreach ($menus as $key => $menu) {
            $this->tester->generateMenuMainMenu($menu);
            $this->translator
                ->shouldReceive('trans')
                ->once()
                ->with($menu['name'], [], TranslationDomain::MAIN_MENU)
                ->andReturn($key);
        }

        // run
        $actual = $this->menuService->getMainMenu();

        // assert
        $this->tester->assertEquals($expected, $actual);
    }

    public function testFindSubMenu(): void
    {
        // setup
        $mainMenuId = $this->tester->generateMenuMainMenu([
            'name' => $mainMenuKey = 'Accounts',
            'link_c' => self::DUMMY_LINK_C,
            'params_c' => '{"dashboardId": "sales-marketing", "mainMenuKey": "sales-marketing"}',
            'icon_c' => 'icon-marketing',
        ]);
        $subMenuId1 = $this->tester->generateDashboard([
            'name' => $subMenuName1 = 'Accounts Dashboard',
            'key_c' => $subMenuKey1 = 'accounts_dashboard',
            'menu_sort_c' => 10,
        ]);
        $this->tester->linkMenuMainMenuToDashboard($mainMenuId, $subMenuId1);

        $subMenuId2 = $this->tester->generateDashboard([
            'name' => $subMenuName2 = 'Finance Dashboard',
            'key_c' => $subMenuKey2 = 'finance_dashboard',
            'menu_sort_c' => 20,
        ]);
        $this->tester->linkMenuMainMenuToDashboard($mainMenuId, $subMenuId2);

        $subMenuId3 = $this->tester->generateDashboard([
            'name' => $subMenuName3 = 'Markering Dashboard',
            'key_c' => $subMenuKey3 = 'markering_dashboard',
            'menu_sort_c' => 30,
        ]);
        $this->tester->linkMenuMainMenuToDashboard($mainMenuId, $subMenuId3);

        $expected = [
            [
                'label' => $subMenuName1,
                'link' => 'dashboard',
                'params' => [
                    'dashboardId' => $subMenuKey1,
                    'mainMenuKey' => $mainMenuKey,
                    'recordId' => null,
                ],
            ],
            [
                'label' => $subMenuName2,
                'link' => 'dashboard',
                'params' => [
                    'dashboardId' => $subMenuKey2,
                    'mainMenuKey' => $mainMenuKey,
                    'recordId' => null,
                ],
            ],
            [
                'label' => $subMenuName3,
                'link' => 'dashboard',
                'params' => [
                    'dashboardId' => $subMenuKey3,
                    'mainMenuKey' => $mainMenuKey,
                    'recordId' => null,
                ],
            ]
        ];

        $this->translator
            ->shouldReceive('trans')
            ->once()
            ->with($subMenuName1, [], TranslationDomain::SUB_MENU)
            ->andReturn($subMenuName1);

        $this->translator
            ->shouldReceive('trans')
            ->once()
            ->with($subMenuName2, [], TranslationDomain::SUB_MENU)
            ->andReturn($subMenuName2);

        $this->translator
            ->shouldReceive('trans')
            ->once()
            ->with($subMenuName3, [], TranslationDomain::SUB_MENU)
            ->andReturn($subMenuName3);

        // run
        $actual = $this->menuService->getSubMenu($mainMenuKey);

        // assert
        $this->tester->assertEquals($expected, $actual);
    }
}
