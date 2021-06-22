<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Service;

use ExEss\Cms\Doctrine\Type\GridType;
use ExEss\Cms\Entity\Dashboard;
use ExEss\Cms\Entity\DashboardMenuAction;
use ExEss\Cms\Entity\GridPanel;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\Entity\Property;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Service\DashboardService;
use Helper\Testcase\FunctionalTestCase;

class DashboardServiceTest extends FunctionalTestCase
{
    private const FILTERS_KEY = 'test-filters-key';
    private const LIST_KEY = 'test-list-key';
    private const GRID_PANEL = 'my-grid';

    private DashboardService $dashboardService;
    private string $recordId;

    public function _before(): void
    {
        $this->dashboardService = $this->tester->grabService(DashboardService::class);
        $this->recordId = $this->tester->generateUser('DashboardServiceTest');
    }

    public function filterConfigDataProvider(): array
    {
        return [
            'no filters' => [
                false,
                [],
                ['display' => false],
            ],
            'with filter' => [
                true,
                [],
                ['display' => true, 'filterKey' => self::FILTERS_KEY]
            ],
            'with grid' => [
                true,
                ['columns' => [['rows' => [['options' => ['listKey' => self::LIST_KEY]]]]]],
                ['display' => true, 'filterKey' => self::FILTERS_KEY, 'listKey' => self::LIST_KEY]
            ],
        ];
    }

    /**
     * @dataProvider filterConfigDataProvider
     */
    public function testFindFiltersConfig(bool $hasFilter, array $grid, array $expectedResponse): void
    {
        // Given
        $user = $this->tester->grabEntityFromRepository(User::class, ['id' => '1']);
        $dashboardId = $this->tester->haveInRepository(Dashboard::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'key' => $dashboardKey = $this->tester->generateUuid(),
            'filter' => !$hasFilter ? null : [
                'createdBy' => $user,
                'dateEntered' => new \DateTime(),
                'key' => self::FILTERS_KEY,
            ],
            'gridTemplate' => [
                'createdBy' => $user,
                'dateEntered' => new \DateTime(),
                'jsonFields' => $grid,
            ],
        ]);
        /** @var Dashboard $dashboard */
        $dashboard = $this->tester->grabEntityFromRepository(Dashboard::class, ['id' => $dashboardId]);

        // When
        $dashboard = $this->dashboardService->getDashboard($dashboard);

        // Then
        $this->tester->assertArrayHasKey('filters', $dashboard);
        $this->tester->assertSame($expectedResponse, $dashboard['filters']);
    }

    public function testCreatePanel(): void
    {
        // Given
        $user = $this->tester->grabEntityFromRepository(User::class, ['id' => '1']);
        $dashboardId = $this->tester->haveInRepository(Dashboard::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'gridTemplate' => [
                'createdBy' => $user,
                'dateEntered' => new \DateTime(),
                'jsonFields' => [
                    'panelKey' => self::GRID_PANEL,
                ],
            ],
        ]);
        $listId = $this->tester->haveInRepository(ListDynamic::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'name' => $listName = $this->tester->generateUuid(),
        ]);
        $this->tester->haveInRepository(GridPanel::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'name' => 'test',
            'size' => '1-1',
            'type' => GridType::LIST,
            'key' => self::GRID_PANEL,
            'list' => $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]),
            'params' => [
                "recordId" => "%recordId%",
                "recordType" => "%recordType%",
            ],
        ]);
        /** @var Dashboard $dashboard */
        $dashboard = $this->tester->grabEntityFromRepository(Dashboard::class, ['id' => $dashboardId]);

        $dashboard->getProperties()->add(
            $this->tester->grabEntityFromRepository(
                Property::class,
                [
                    'id' => $this->tester->haveInRepository(Property::class, [
                        'createdBy' => $user,
                        'dateEntered' => new \DateTime(),
                        'name' => 'panelKey',
                    ]),
                ]
            )
        );

        // When
        $dashboard = $this->dashboardService->getDashboard(
            $dashboard,
            ['recordType' => User::class],
            $this->recordId
        );

        // Then
        $this->tester->assertArrayHasKey('grid', $dashboard);
        $grid = $dashboard['grid'];
        $this->tester->assertSame(
            [
                'size' => '1-1',
                'type' => 'list',
                'options' => [
                    'listKey' => $listName,
                    'params' => [
                        'recordId' => $this->recordId,
                        'recordType' => User::class,
                    ],
                ],
            ],
            $grid
        );
    }

    public function testGetButtonConfig(): void
    {
        // Given
        $user = $this->tester->grabEntityFromRepository(User::class, ['id' => '1']);
        $dashboardId = $this->tester->haveInRepository(Dashboard::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'dashboardMenu' => [
                'createdBy' => $user,
                'dateEntered' => new \DateTime(),
            ],
        ]);
        /** @var Dashboard $dashboard */
        $dashboard = $this->tester->grabEntityFromRepository(Dashboard::class, ['id' => $dashboardId]);

        $params = ['foo' => 'bar', 'recordId' => '%recordId%', 'recordType' => User::class];
        $menuActionId = $this->tester->haveInRepository(DashboardMenuAction::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'flowAction' => [
                'createdBy' => $user,
                'dateEntered' => new \DateTime(),
                'guid' => $actionGuid = $this->tester->generateUuid(),
            ],
            'label' => 'action_test',
            'icon' => 'test icon',
            'sortOrder' => 1,
            'params' => $params,
        ]);
        $dashboard->getDashboardMenu()->getActions()->add(
            $this->tester->grabEntityFromRepository(DashboardMenuAction::class, ['id' => $menuActionId])
        );

        // When
        $dashboard = $this->dashboardService->getDashboard(
            $dashboard,
            ['recordType' => User::class],
            $this->recordId
        );

        // Then
        $this->tester->assertArrayHasKey('plusMenu', $dashboard);
        $this->tester->assertArrayHasKey('buttons', $dashboard['plusMenu']);
        $buttons = $dashboard['plusMenu']['buttons'];
        $this->tester->assertCount(1, $buttons);
        $this->tester->assertEquals(
            [
                'enabled' => true,
                'label' => 'action_test',
                'icon' => 'test icon',
                'sort_order' => 1,
                'buttonGroup' => false,
                'action' => [
                    'id' => $actionGuid,
                    'foo' => 'bar',
                    'recordId' => $this->recordId,
                    'recordType' => User::class,
                ]
            ],
            $buttons[0]
        );
    }

    public function testFindFiltersConfigWithoutLinkedSearch(): void
    {
        // Given
        $user = $this->tester->grabEntityFromRepository(User::class, ['id' => '1']);
        $dashboardId = $this->tester->haveInRepository(Dashboard::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
        ]);
        /** @var Dashboard $dashboard */
        $dashboard = $this->tester->grabEntityFromRepository(Dashboard::class, ['id' => $dashboardId]);

        // When
        $dashboard = $this->dashboardService->getDashboard($dashboard);

        // Then
        $this->tester->assertArrayHasKey('search', $dashboard);
        $search = $dashboard['search'];
        $this->tester->assertArrayHasKey('display', $search);
        $this->tester->assertFalse($search['display']);
    }

    public function testGetSearchConfigWithSearch(): void
    {
        // Given
        $params = ['test' => 'test'];
        $user = $this->tester->grabEntityFromRepository(User::class, ['id' => '1']);
        $dashboardId = $this->tester->haveInRepository(Dashboard::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'search' => [
                'createdBy' => $user,
                'dateEntered' => new \DateTime(),
                'params' => $params,
                'linkTo' => 'dashboard',
            ],
        ]);
        /** @var Dashboard $dashboard */
        $dashboard = $this->tester->grabEntityFromRepository(Dashboard::class, ['id' => $dashboardId]);

        // When
        $dashboard = $this->dashboardService->getDashboard($dashboard);

        // Then
        $this->tester->assertArrayHasKey('search', $dashboard);
        $search = $dashboard['search'];
        $this->tester->assertSame(['display' => true, 'linkTo' => 'dashboard', 'params' => $params], $search);
    }

    public function testUnauthorizedBlocks(): void
    {
        // Given
        $listName = 'test-1';
        $this->tester->generateDynamicList(['name' => $listName]);

        $user = $this->tester->grabEntityFromRepository(User::class, ['id' => '1']);
        $dashboardId = $this->tester->haveInRepository(Dashboard::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'gridTemplate' => [
                'createdBy' => $user,
                'dateEntered' => new \DateTime(),
                'jsonFields' => [
                    'columns' => [
                        'size' => '1-1',
                        'hasMargin' => false,
                        'rows' => [
                            [
                                'size' => '',
                                'type' => 'embeddedGuidance',
                                'cssClasses' => ['card', '%crm-expr%', '{%dwp-expr%}'],
                                'options' => ['recordType' => 'Users', 'flowId' => 'gf_home_welcome',],
                            ],
                            [
                                'type' => 'list',
                                'options' => ['listKey' => $listName, 'params' => []],
                            ],
                            [
                                'size' => '',
                                'type' => 'list',
                                'options' => ['listKey' => 'test-2', 'params' => []],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        /** @var Dashboard $dashboard */
        $dashboard = $this->tester->grabEntityFromRepository(Dashboard::class, ['id' => $dashboardId]);

        // When
        $dashboard = $this->dashboardService->getDashboard($dashboard, [
            'crm-expr' => 'crm',
            'dwp-expr' => 'dwp',
        ]);

        // Then
        $this->tester->assertArrayHasKey('grid', $dashboard);
        $grid = $dashboard['grid'];
        $this->tester->assertEquals('embeddedGuidance', $grid['columns']['rows'][0]['type']);
        $this->tester->assertEquals(['card', 'crm', '{%dwp-expr%}'], $grid['columns']['rows'][0]['cssClasses']);
        $this->tester->assertEquals('list', $grid['columns']['rows'][1]['type']);
        $this->tester->assertEquals('unauthorized-list', $grid['columns']['rows'][2]['type']);
    }
}
