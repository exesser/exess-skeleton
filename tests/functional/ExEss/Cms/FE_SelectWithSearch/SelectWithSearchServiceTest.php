<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\FE_SelectWithSearch;

use ExEss\Cms\Doctrine\Type\UserStatus;
use ExEss\Cms\Entity\User;
use Helper\Testcase\FunctionalTestCase;
use ExEss\Cms\FESelectWithSearch\SelectWithSearchService;

class SelectWithSearchServiceTest extends FunctionalTestCase
{
    private SelectWithSearchService $selectWithSearchService;

    public function _before(): void
    {
        $this->selectWithSearchService = $this->tester->grabService(SelectWithSearchService::class);
    }

    public function testGetMultiSelectItems(): void
    {
        $this->tester->generateSelectWithSearchDatasource([
            'name' => "Users12345",
            'items_on_page' => 45,
            'base_object' => User::class,
            'order_by' => 'sws.firstName, sws.lastName',
            'filters' => "JOIN sws.userGroups su WHERE su.securityGroup = '%for_pg%' AND sws.status = 'Active'",
            'option_label' => '%userName% - %firstName% %lastName%',
            'option_key' => '%id%',
            'filter_string' => '%lastName%',
        ]);

        $primaryGroup = $this->tester->generateSecurityGroup('testgroup');

        $user1 = $this->tester->generateUser('wky', [
            'first_name' => 'Bogdan',
            'last_name' => 'Terzea',
            'status' => UserStatus::ACTIVE,
        ]);
        $this->tester->linkUserToSecurityGroup($user1, $primaryGroup);

        $user2 = $this->tester->generateUser('KBlock', [
            'first_name' => 'Ken',
            'last_name' => 'Block',
            'status' => UserStatus::ACTIVE,
        ]);
        $this->tester->linkUserToSecurityGroup($user2, $primaryGroup);

        $this->tester->generateUser('THansen', [
            'first_name' => 'Timmy',
            'last_name' => 'Hansen',
            'status' => UserStatus::INACTIVE,
        ]);

        $this->tester->assertSame(
            [
                'rows' => [
                    [
                        "key" => $user1,
                        "label" => "wky - Bogdan Terzea"
                    ],
                ],
                'pagination' => [
                    'page' => 1,
                    'pages' => 1,
                    'pageSize' => 45,
                    'total' => 1
                ]
            ],
            $this->selectWithSearchService->getSelectOptions('Users12345', [
                "page" => 1,
                'fullModel' => [
                    'for_pg' => $primaryGroup,
                    'dwp|bla|for_pg' => null,
                ],
                'query' => 'Terzea',
            ])
        );

        $this->tester->assertSame(
            [
                'rows' => [
                    [
                        "key" => $user1,
                        "label" => "wky - Bogdan Terzea"
                    ],
                    [
                        "key" => $user2,
                        "label" => "KBlock - Ken Block"
                    ],
                ],
                'pagination' => [
                    'page' => 1,
                    'pages' => 1,
                    'pageSize' => 45,
                    'total' => 2
                ]
            ],
            $this->selectWithSearchService->getSelectOptions('Users12345', [
                "page" => 1,
                'fullModel' => [
                    'for_pg' => $primaryGroup,
                ],
            ])
        );
    }

    public function orderDataProvider(): array
    {
        return [
            ['sws.lastName'],
            [''],
            [null],
        ];
    }

    /**
     * @dataProvider orderDataProvider
     */
    public function testGetLabelsForValues(?string $orderBy): void
    {
        $this->tester->generateSelectWithSearchDatasource([
            'name' => 'ContactPersons12345',
            'items_on_page' => 45,
            'base_object' => User::class,
            'order_by' => $orderBy,
            'filters' => '',
            'option_label' => '%firstName% %lastName%',
            'option_key' => '%id%',
            'filter_string' => '%lastName%',
        ]);

        $expectedItems = [];

        foreach (\range(1, 150) as $key) {
            $firstName = 'Ken';
            $lastName = \sprintf("Block %03d", $key);
            $id = $this->tester->generateUser("user$key", [
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);

            $expectedItems[] = [
                "key" => $id,
                "label" => "$firstName $lastName"
            ];
        }

        $this->tester->generateUser("TimmyHansen", [
            'first_name' => 'Timmy',
            'last_name' => 'Hansen',
        ]);

        $result = $this->selectWithSearchService->getLabelsForValues(
            'ContactPersons12345',
            \array_column($expectedItems, 'key')
        );

        foreach ($expectedItems as $item) {
            $this->tester->assertContains($item, $result);
        }

        $this->tester->assertSame(
            [],
            $this->selectWithSearchService->getLabelsForValues(
                'ContactPersons12345',
                ['a-key-that-does-not-exists']
            )
        );
    }

    public function testEquals(): void
    {
        $this->tester->generateSelectWithSearchDatasource([
            'name' => 'ContactPersons12345',
            'items_on_page' => 45,
            'base_object' => User::class,
            'option_label' => '%firstName% %lastName%',
            'option_key' => '%id%',
            'filter_string' => 'lastName',
        ]);

        $this->tester->generateUser("TimmyHansen", [
            'first_name' => 'Timmy',
            'last_name' => 'Hansen',
        ]);

        $this->tester->generateUser("TimmyHansenworst", [
            'first_name' => 'Timmy',
            'last_name' => 'Hansenworst',
        ]);

        $result = $this->selectWithSearchService->getSelectOptions('ContactPersons12345', [
            'query' => 'Hansen',
        ]);

        $this->tester->assertCount(1, $result['rows']);
        $this->tester->assertEquals('Timmy Hansen', $result['rows'][0]['label']);
    }

    public function testNeedsQuery(): void
    {
        $this->tester->generateSelectWithSearchDatasource([
            'name' => "Users12345",
            'items_on_page' => 45,
            'needs_query' => true,
            'base_object' => User::class,
        ]);

        $this->tester->assertSame(
            [
                'rows' => [],
                'pagination' => [
                    'page' => 1,
                    'pages' => 1,
                    'pageSize' => 45,
                    'total' => 0
                ]
            ],
            $this->selectWithSearchService->getSelectOptions('Users12345', [
                "page" => 1,
                'fullModel' => [],
            ])
        );
    }
}
