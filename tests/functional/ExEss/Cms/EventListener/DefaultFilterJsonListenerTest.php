<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\EventListener;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Entity\FilterField;
use ExEss\Cms\Entity\FilterFieldGroup;
use Helper\Testcase\FunctionalTestCase;

/**
 * @see DefaultFilterJsonListener
 */
class DefaultFilterJsonListenerTest extends FunctionalTestCase
{
    private EntityManager $em;

    public function _before(): void
    {
        $this->em = $this->tester->grabService('doctrine.orm.entity_manager');
    }

    /**
     * @dataProvider fieldDataProvider
     */
    public function testWithUpdatedFilterField(
        string $operator,
        array $filterJson,
        string $newOperator,
        array $expectedFilterJson,
        bool $expectListFilterChanged
    ): void {
        // Given
        [$groupId, $fieldId] = $this->setupFilter($filterJson, $operator);
        /** @var FilterField $field */
        $field = $this->em->getRepository(FilterField::class)->find($fieldId);

        // When
        $field->setOperator($newOperator);
        $this->em->persist($field);
        $this->em->flush();

        // Then
        $filter = $field->getGroups()[0]->getFilters()[0];
        $this->tester->assertEquals($expectedFilterJson, $filter->getDefaultFiltersJson());
        $this->tester->assertEquals($expectListFilterChanged, $filter->getLists()[0]->getFiltersHaveChanged());
    }

    public function fieldDataProvider(): array
    {
        $equals = [
            'ean1' => [
                'default' => [
                    'value' => '',
                    'operator' => '=',
                ]
            ],
            'ean2' => [
                'default' => [
                    'value' => '',
                    'operator' => '=',
                ]
            ],
        ];

        $changed = $equals;
        $changed['ean1']['default']['operator'] = '>';

        return [
            'operator changes' => [
                '>',
                $changed,
                '=',
                $equals,
                true
            ],
            'no changes' => [
                '=',
                $equals,
                '=',
                $equals,
                false
            ],
        ];
    }

    /**
     * @dataProvider groupDataProvider
     */
    public function testWithUpdatedFilterGroup(
        array $filterJson,
        string $newSort,
        array $expectedFilterJson,
        bool $expectListFilterChanged
    ): void {
        // Given
        [$groupId, $fieldId] = $this->setupFilter($filterJson);
        /** @var FilterFieldGroup $group */
        $group = $this->em->getRepository(FilterFieldGroup::class)->find($groupId);

        // When
        $group->setSort($newSort);
        $this->em->persist($group);
        $this->em->flush();

        // Then
        $filter = $group->getFilters()[0];
        $this->tester->assertEquals($expectedFilterJson, $filter->getDefaultFiltersJson());
        $this->tester->assertEquals($expectListFilterChanged, $filter->getLists()[0]->getFiltersHaveChanged());
    }

    public function groupDataProvider(): array
    {
        $filter1 = [
            'ean1' => [
                'default' => [
                    'value' => '',
                    'operator' => '=',
                ]
            ],
        ];
        $filter2 = [
            'ean2' => [
                'default' => [
                    'value' => '',
                    'operator' => '=',
                ]
            ],
        ];

        $first1 = \array_merge($filter1, $filter2);
        $first2 = \array_merge($filter2, $filter1);

        return [
            'sorting changed' => [
                $first1,
                '30',
                $first2,
                true
            ],
            'no changes' => [
                $first1,
                '11',
                $first1,
                false
            ],
        ];
    }

    private function setupFilter(array $filterJson, string $operator = '='): array
    {
        $filterId = $this->tester->generateFilter([
            'default_filters_json_c' => \json_encode($filterJson),
        ]);
        $this->tester->generateDynamicList([
            'filters_have_changed' => 0,
            'filter_id' => $filterId,
        ]);

        $this->tester->linkFilterFieldToFieldGroup(
            $fieldId1 = $this->tester->generateFilterField([
                'field_key_c' => 'ean1',
                'operator' => $operator,
            ]),
            $groupId1 = $this->tester->generateFilterFieldGroup([
                'sort_c' => '10',
            ])
        );
        $this->tester->linkFilterFieldToFieldGroup(
            $this->tester->generateFilterField([
                'field_key_c' => 'ean2',
                'operator' => '=',
            ]),
            $groupId2 = $this->tester->generateFilterFieldGroup([
                'sort_c' => '20',
            ])
        );
        $this->tester->linkFilterToFieldGroup($filterId, $groupId1);
        $this->tester->linkFilterToFieldGroup($filterId, $groupId2);

        return [$groupId1, $fieldId1];
    }
}
