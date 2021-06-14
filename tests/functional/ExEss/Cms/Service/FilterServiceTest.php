<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Doctrine\Type\SecurityGroupType;
use ExEss\Cms\Doctrine\Type\UserStatus;
use ExEss\Cms\Entity\FilterField;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Service\FilterService;
use ExEss\Cms\Test\Testcase\FunctionalTestCase;

class FilterServiceTest extends FunctionalTestCase
{
    private string $listId;
    private string $fieldId1;
    private string $fieldId2;
    private FilterService $filterService;
    private EntityManagerInterface $em;

    public function _before(): void
    {
        $this->em = $this->tester->grabService('doctrine.orm.entity_manager');
        $this->filterService = $this->tester->grabService(FilterService::class);

        $this->listId = $this->tester->generateDynamicList([
            'name' => $this->tester->generateUuid(),
            'filter_id' => $filterId = $this->tester->generateFilter(),
        ]);

        $groupId1 = $this->tester->generateFilterFieldGroup(['sort_c' => '10',]);
        $groupId2 = $this->tester->generateFilterFieldGroup(['sort_c' => '20',]);

        $this->fieldId1 = $this->tester->generateFilterField([
            'field_key_c' => 'title',
            'label_c' => 'My title',
            'operator' => '=',
            'field_options_c' => '{"enumValues":{"Large": {"value": "Large"}, "Medium": {"value": "Medium"}}}',
            'field_options_enum_key_c' => '',
        ]);

        $this->fieldId2 = $this->tester->generateFilterField([
            'field_key_c' => 'name',
            'label_c' => 'My name',
            'operator' => '=',
            'field_options_enum_key_c' => (new UserStatus)->getName(),
        ]);

        $this->tester->linkFilterFieldToFieldGroup($this->fieldId1, $groupId1);
        $this->tester->linkFilterFieldToFieldGroup($this->fieldId2, $groupId2);
        $this->tester->linkFilterToFieldGroup($filterId, $groupId1);
        $this->tester->linkFilterToFieldGroup($filterId, $groupId2);
    }

    public function testAddFilterConditions(): void
    {
        // Given
        $qb = $this->em->getRepository(ListDynamic::class)->createQueryBuilder('test');
        $qb->select("test.id");

        // When
        $this->filterService->addFilterConditions(
            'test',
            $qb,
            new ArrayCollection([
                $this->tester->grabEntityFromRepository(FilterField::class, ['id' => $this->fieldId1]),
                $this->tester->grabEntityFromRepository(FilterField::class, ['id' => $this->fieldId2]),
            ]),
            [
                "title" => [
                    "default" => [
                        "value" => ["Large"],
                        "fieldId" => $this->fieldId1,
                    ]
                ],
                "name" => [
                    "default" => [
                        "value" => ["bvba", "ebvba"],
                        "fieldId" => $this->fieldId1,
                    ],
                ],
            ],
        );

        // Then
        $this->tester->assertEquals(
            "SELECT test.id FROM " . ListDynamic::class. " test "
            . "WHERE test.title IN('Large') AND test.name IN('bvba', 'ebvba')",
            $qb->getDQL()
        );
        $this->tester->assertEquals(
            "SELECT l0_.id AS id_0 FROM list_dynamic_list l0_ "
            . "WHERE l0_.title IN ('Large') AND l0_.name IN ('bvba', 'ebvba')",
            $qb->getQuery()->getSQL()
        );
    }

    public function filterDataProvider(): array
    {
        return [
            [
                'users_I_id',
                FilterService::CURRENT_USER_ID,
                'unique_user_id',
                'userId',
                'userName',
            ],
            [
                'sql_I_primary_group',
                FilterService::CURRENT_PRIMARY_GROUP_ID,
                'unique_primary_group_id',
                'customerSecurityGroupId',
                'customerSecurityGroupName'
            ],
        ];
    }

    /**
     * @dataProvider filterDataProvider
     */
    public function testReplaceDefaultValues(
        string $fieldName,
        string $defaultValue,
        string $fieldId,
        string $propertyName,
        string $propertyValue
    ): void {
        // Given
        $userId = $this->tester->generateUser('userName', ['first_name' => "userName"]);

        $this->tester->linkUserToSecurityGroup(
            $userId,
            $customerSecurityGroupId = $this->tester->generateSecurityGroup('customerSecurityGroupName', [
                'main_groups_c' => SecurityGroupType::EMPLOYEE
            ]),
            ['primary_group' => 1]
        );

        $this->tester->loginAsUser(
            $this->tester->grabEntityFromRepository(User::class, ['id' => $userId])
        );

        $defaultFiltersArray = [
            $fieldName => [
                'default' => [
                    "value" => [
                        $defaultValue,
                    ],
                    "fieldId" => $fieldId,
                ],
            ],
        ];

        // When
        $result = $this->filterService->replaceDefaultValues($defaultFiltersArray);

        // Then
        $this->tester->assertIsArray($result);
        $this->tester->assertEquals($result[$fieldName]['default']['value'][1]['key'], $$propertyName);
        $this->tester->assertEquals($result[$fieldName]['default']['value'][1]['label'], $propertyValue);
    }

    public function testGetFilters(): void
    {
        // Given
        /** @var ListDynamic $list */
        $list = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $this->listId]);

        // When
        $filters = $this->filterService->getFilters($list);

        // Then
        $this->tester->assertEquals(
            $this->tester->loadJsonWithParams(
                __DIR__ . '/resources/FilterServiceTest-getFilters.json',
                [
                    'fieldId1' => $this->fieldId1,
                    'fieldId2' => $this->fieldId2,
                ]
            ),
            \json_decode(\json_encode($filters), true)
        );
    }
}
