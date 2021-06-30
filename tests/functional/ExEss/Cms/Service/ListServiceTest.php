<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Service;

use Doctrine\Common\Collections\ArrayCollection;
use ExEss\Bundle\CmsBundle\Controller\ListDynamic\Body\ListBody;
use ExEss\Bundle\CmsBundle\Doctrine\Type\CellType;
use ExEss\Bundle\CmsBundle\Doctrine\Type\Locale;
use ExEss\Bundle\CmsBundle\Doctrine\Type\SecurityGroupType;
use ExEss\Bundle\CmsBundle\Doctrine\Type\TranslationDomain;
use ExEss\Bundle\CmsBundle\Entity\ListDynamic;
use ExEss\Bundle\CmsBundle\Entity\ListSortingOption;
use ExEss\Bundle\CmsBundle\Entity\ListTopAction;
use ExEss\Bundle\CmsBundle\Entity\ListTopBar;
use ExEss\Bundle\CmsBundle\Entity\Translation;
use ExEss\Bundle\CmsBundle\Entity\User;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
use ExEss\Bundle\CmsBundle\Http\Factory\JsonBodyFactory;
use ExEss\Bundle\CmsBundle\ListFunctions\HelperClasses\DynamicListResponse;
use ExEss\Bundle\CmsBundle\Service\ListService;
use Helper\Testcase\FunctionalTestCase;

class ListServiceTest extends FunctionalTestCase
{
    private ?string $sortingOptionId = null;
    private ListService $listService;
    private ListBody $body;
    private string $userId;

    public function _before(): void
    {
        $this->tester->deleteFromDatabase('users', ['first_name' => self::class]);

        // generate 5 accounts
        $this->tester->generateUser('', ['first_name' => self::class]);
        $this->tester->generateUser('', ['first_name' => self::class]);
        $this->tester->generateUser('', ['first_name' => self::class]);
        $this->tester->generateUser('', ['first_name' => self::class]);
        $this->tester->generateUser('', ['first_name' => self::class]);

        // set up and log in user
        $this->userId = $this->tester->generateUser('test@exesser.be', [
            'preferred_locale' => 'nl_BE',
        ]);
        $this->tester->linkUserToRole($this->userId, User::ROLE_ADMIN);
        $this->tester->linkUserToSecurityGroup(
            $this->userId,
            $this->tester->generateSecurityGroup(
                'employee group',
                ['main_groups_c' => SecurityGroupType::EMPLOYEE]
            ),
            ['primary_group' => 1]
        );
        $this->tester->linkUserToSecurityGroup(
            $this->userId,
            $this->tester->generateSecurityGroup(
                'employee group',
                ['main_groups_c' => SecurityGroupType::DEALER]
            ),
            ['primary_group' => 1]
        );

        $this->listService = $this->tester->grabService(ListService::class);
        $this->body = $this->tester->grabService(JsonBodyFactory::class)->create(ListBody::class);
    }

    private function setupDataList(int $fixPagination, int $itemsPerPage = 10): string
    {
        $user = $this->tester->grabEntityFromRepository(User::class, ['id' => '1']);
        $topBarId = $this->tester->haveInRepository(ListTopBar::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'selectAll' => true,
        ]);
        $this->sortingOptionId = $this->tester->haveInRepository(ListSortingOption::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'sortKey' => 'base.userName',
            'orderBy' => 'DESC',
        ]);
        /** @var ListTopBar $topBar */
        $topBar = $this->tester->grabEntityFromRepository(ListTopBar::class, ['id' => $topBarId]);
        /** @var ListSortingOption $sortingOption */
        $sortingOption = $this->tester->grabEntityFromRepository(
            ListSortingOption::class,
            ['id' => $this->sortingOptionId]
        );
        $topBar->setSortingOptions(new ArrayCollection([$sortingOption]));

        $listId = $this->tester->haveInRepository(ListDynamic::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'name' => $listName = $this->tester->generateUuid(),
            'fixPagination' => $fixPagination,
            'itemsPerPage' => $itemsPerPage,
            'baseObject' => User::class,
            'topBar' => $topBar,
            'cellLinks' => [
                [
                    'createdBy' => $user,
                    'dateEntered' => new \DateTime(),
                    'order_c' => 1,
                    'cell' => [
                        'createdBy' => $user,
                        'dateEntered' => new \DateTime(),
                    ],
                ],
                [
                    'createdBy' => $user,
                    'dateEntered' => new \DateTime(),
                    'order_c' => 2,
                    'cell' => [
                        'createdBy' => $user,
                        'dateEntered' => new \DateTime(),
                    ],
                ],
                [
                    'createdBy' => $user,
                    'dateEntered' => new \DateTime(),
                    'order_c' => 3,
                    'cell' => [
                        'createdBy' => $user,
                        'dateEntered' => new \DateTime(),
                    ],
                ],
                [
                    'createdBy' => $user,
                    'dateEntered' => new \DateTime(),
                    'order_c' => 4,
                    'cell' => [
                        'createdBy' => $user,
                        'dateEntered' => new \DateTime(),
                    ],
                ],
                [
                    'createdBy' => $user,
                    'dateEntered' => new \DateTime(),
                    'order_c' => 5,
                    'cell' => [
                        'createdBy' => $user,
                        'dateEntered' => new \DateTime(),
                    ],
                ],
                [
                    'createdBy' => $user,
                    'dateEntered' => new \DateTime(),
                    'order_c' => 6,
                    'cell' => [
                        'createdBy' => $user,
                        'dateEntered' => new \DateTime(),
                        'type' => CellType::SIMPLE_TWO_LINER,
                        'line1' => '%userName%',
                    ],
                ],
                [
                    'createdBy' => $user,
                    'dateEntered' => new \DateTime(),
                    'order_c' => 7,
                    'cell' => [
                        'createdBy' => $user,
                        'dateEntered' => new \DateTime(),
                    ],
                ],
            ],
        ]);

        $this->tester->loginAsUser(
            $this->tester->grabEntityFromRepository(User::class, ['id' => $this->userId])
        );

        return $listId;
    }

    public function testGetList(): void
    {
        // setup
        $listId = $this->setupDataList(1);
        $this->body->configure([
            'params' => [],
        ]);
        $list = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]);

        // act
        $response = $this->listService->getList($list, $this->body);

        // assert
        $this->tester->assertGreaterThan(5, \count($response->rows));    // we added 5 in _before
        $this->tester->assertIsObject($response->pagination);
        $this->tester->assertGreaterThan(5, (int) $response->pagination->total);
        $this->tester->assertIsObject($response->topBar);
        $this->tester->assertCount(1, $response->topBar->sortingOptions);
        $this->tester->assertCount(7, $response->headers);
    }

    public function testGetOnlyRecordCount(): void
    {
        // Given
        $listId = $this->setupDataList(1);
        $this->body->configure([
            'params' => [],
            'onlyRecordCount' => true,
        ]);
        $list = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]);

        // When
        $response = $this->listService->getList($list, $this->body);

        // Then
        $this->tester->assertIsObject($response->pagination);
        $this->tester->assertGreaterThan(5, (int) $response->pagination->total);
    }

    public function testCleanListCell(): void
    {
        // Given
        $this->tester->generateUser('ZZZ {{2*2}}');
        $listId = $this->setupDataList(1);
        $this->body->configure([
            'params' => [],
            'sortBy' => $this->sortingOptionId,
        ]);
        $list = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]);

        // When
        $response = $this->listService->getList($list, $this->body);

        // Then
        $this->tester->assertEquals('ZZZ [[2*2]]', $response->rows[0]->cells[5]->options->line1);
    }

    public function getListWithoutPaginationDataProvider(): array
    {
        return [
            'page1' => [
                1,
                [
                    'page' => 1,
                    'size' => 2,
                    'sortBy' => 'base.dateEntered DESC',
                ]
            ],
            'page2' => [
                2,
                [
                    'page' => 2,
                    'size' => 2,
                    'sortBy' => 'base.dateEntered DESC',
                ]
            ],
            'page3' => [
                3,
                [
                    'page' => 3,
                    'size' => 2,
                    'sortBy' => 'base.dateEntered DESC',
                ]
            ],

        ];
    }

    /**
     * @dataProvider getListWithoutPaginationDataProvider
     */
    public function testGetListWithoutPagination(int $page, array $pagination): void
    {
        // Given
        $listId = $this->setupDataList(0, 2);
        $this->body->configure([
            'params' => [],
            'page' => $page,
        ]);
        $list = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]);

        // When
        $response = $this->listService->getList($list, $this->body);

        // Then
        $responsePagination = DataCleaner::jsonDecode(\json_encode($response->pagination));
        unset($responsePagination['total']);
        $this->tester->assertGreaterThan(1, $responsePagination['pages']);
        unset($responsePagination['pages']);

        $this->tester->assertArrayEqual($pagination, $responsePagination);
    }

    public function testGetNoTopBarForList(): void
    {
        // Given
        $user = $this->tester->grabEntityFromRepository(User::class, ['id' => '1']);
        $listId = $this->tester->haveInRepository(ListDynamic::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'name' => $listName = $this->tester->generateUuid(),
            'fixPagination' => 0,
            'itemsPerPage' => 2,
            'baseObject' => User::class,
        ]);
        $response = new DynamicListResponse();
        $list = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]);

        // When
        $this->listService->fillTopBarOnList(
            $list,
            $this->body,
            $response,
        );

        // Then
        $this->tester->assertFalse($response->topBar);
    }

    public function testGetTopBarForListWithoutActionsAndSortingOptions(): void
    {
        // Given
        $user = $this->tester->grabEntityFromRepository(User::class, ['id' => '1']);
        $listId = $this->tester->haveInRepository(ListDynamic::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'name' => $listName = $this->tester->generateUuid(),
            'fixPagination' => 0,
            'itemsPerPage' => 2,
            'baseObject' => User::class,
            'topBar' => [
                'createdBy' => $user,
                'dateEntered' => new \DateTime(),
                'selectAll' => true,
            ],
        ]);
        $response = new DynamicListResponse();

        $list = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]);

        // When
        $this->listService->fillTopBarOnList(
            $list,
            $this->body,
            $response,
        );

        // Then
        $this->tester->assertTrue($response->topBar->selectAll);
        $this->tester->assertCount(0, $response->topBar->buttons);
        $this->tester->assertCount(0, $response->topBar->sortingOptions);
    }

    public function testGetTopBarForListFull(): void
    {
        // Given
        $recordId = $this->tester->generateUser("test user");
        $response = new DynamicListResponse();
        $listId = $this->setupDataList(1);

        $this->body->configure([
            'recordId' => $recordId,
            'recordType' => User::class,
            'params' => [],
        ]);

        $list = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]);

        $user = $this->tester->grabEntityFromRepository(User::class, ['id' => '1']);
        $toTranslate = 'mandatorySelectRecordMessageTrans';
        $topActionId = $this->tester->haveInRepository(ListTopAction::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'name' => $actionName = $this->tester->generateUuid(),
            'flowAction' => [
                'createdBy' => $user,
                'dateEntered' => new \DateTime(),
                'guid' => $actionGuid = $this->tester->generateUuid(),
            ],
            'params' => [
                "name" => "TEST",
                "accountId" => "%recordId%",
                "mandatorySelectRecord" => true,
                "mandatorySelectRecordMessage" => "$toTranslate",
            ],
            "icon" => $actionIcon = $this->tester->generateUuid(),
        ]);
        /** @var ListTopAction $topBar */
        $action = $this->tester->grabEntityFromRepository(ListTopAction::class, ['id' => $topActionId]);
        $list->getTopBar()->setActions(new ArrayCollection([$action]));

        $this->tester->haveInRepository(Translation::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'name' => $toTranslate,
            'translation' => $translated = 'mandatorySelectRecordMessageTransNl',
            'locale' => Locale::NL,
            'domain' => TranslationDomain::LIST_TOPBAR,
        ]);

        // When
        $this->listService->fillTopBarOnList(
            $list,
            $this->body,
            $response,
        );

        // Then
        $this->tester->assertTrue($response->topBar->selectAll);
        $this->tester->assertCount(1, $response->topBar->buttons);

        $button = \array_pop($response->topBar->buttons);
        $this->tester->assertSame($button->label, $actionName);
        $this->tester->assertSame($button->CALLBACK, 'CALLBACK');
        $this->tester->assertSame(
            [
                'id' => $actionGuid,
                'name' => 'TEST',
                'accountId' => $recordId,
                'mandatorySelectRecord' => true,
                'mandatorySelectRecordMessage' => $translated,
            ],
            $button->action
        );
        $this->tester->assertSame($button->icon, $actionIcon);
        $this->tester->assertCount(1, $response->topBar->sortingOptions);
    }
}
