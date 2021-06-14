<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Lists;

use ApiTester;
use Codeception\Example;
use ExEss\Cms\Doctrine\Type\CellType;
use ExEss\Cms\Entity\FlowField;
use ExEss\Cms\Entity\ListSortingOption;

/**
 * tests @see RelationsRepository::findBy()
 */
class GetRelationsListCest
{
    private string $listName;

    private string $stepNameFilterId;

    private string $fieldNameFilterId;

    public function _before(ApiTester $I): void
    {
        // Given
        $flow = $I->generateFlow();
        $step1 = $I->generateFlowSteps($flow, ['name' => 'Ken']);
        $step2 = $I->generateFlowSteps($flow, ['name' => 'Andreas']);

        // create some data
        for ($i = 1; $i <= 30; $i++) {
            $suffix = \sprintf("%02d", $i);
            $field = $I->generateGuidanceField([
                'name' => "GroupForRel$suffix",
                'date_entered' => \date('Y-m-d H:i:') . ($i + 10),
                'field_id' => "GROUP$suffix",
            ]);
            $I->linkGuidanceFieldToFlowStep($field, ($i <= 15 ? $step2 : $step1));
        }

        // setup list
        $listId = $I->generateDynamicList([
            'name' => $this->listName = $I->generateUuid(),
            'base_object' => FlowField::class . '::steps',
            'items_per_page' => 25,
            'filter_id' => $filterId = $I->generateFilter(),
            'external_object_id' => $I->generateListExternalObject([
                'class_handler' => 'relationsHandler',
            ])
        ]);
        $I->generateListCellForList(
            $listId,
            [
                'type' => CellType::SIMPLE_TWO_LINER,
                'line1' => '%fields|name%',
                'line2' => '%fields|fieldId%',
            ],
            ['order_c' => 10]
        );
        $I->generateListCellForList(
            $listId,
            [
                'type' => CellType::LINK_BOLD_TOP_TWO_LINER,
                'line1' => '%steps|name%',
                'line2' => '%steps|type%',
            ],
            ['order_c' => 20]
        );
        $I->linkFilterToFieldGroup(
            $filterId,
            $filterFieldGroupId = $I->generateFilterFieldGroup()
        );
        $I->linkFilterFieldToFieldGroup(
            $this->fieldNameFilterId = $I->generateFilterField([
                'operator' => '=',
                'field_key_c' => 'fields|name',
            ]),
            $filterFieldGroupId
        );
        $I->linkFilterFieldToFieldGroup(
            $this->stepNameFilterId = $I->generateFilterField([
                'operator' => '=',
                'field_key_c' => 'steps|name',
            ]),
            $filterFieldGroupId
        );
    }

    public function getList(ApiTester $I): void
    {
        $params = [
            'sortBy' => $I->generateListSortingOptions([
                'sort_key' => ListSortingOption::DEFAULT_SORT,
                'order_by' => ListSortingOption::DEFAULT_ORDER,
            ]),
        ];

        // Given
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPOST("/Api/V8_Custom/List/{$this->listName}", $params);

        // Then
        $I->seeResponseIsDwpResponse(200);

        $I->assertCount(25, $I->grabDataFromResponseByJsonPath('$.data.rows')[0]);
        $I->seeAssertPathsInJson([
            '$.data.rows[0].cells[0].options.line1' => 'GroupForRel30',
            '$.data.rows[0].cells[0].options.line2' => 'GROUP30',
            '$.data.rows[0].cells[1].options.line1' => 'Ken',
            '$.data.rows[0].cells[1].options.line2' => 'DEFAULT',
            '$.data.rows[20].cells[0].options.line1' => 'GroupForRel10',
            '$.data.rows[20].cells[0].options.line2' => 'GROUP10',
            '$.data.rows[20].cells[1].options.line1' => 'Andreas',
            '$.data.rows[20].cells[1].options.line2' => 'DEFAULT',
        ]);
    }

    /**
     * @dataProvider provider
     */
    public function getWithPageAndFilters(ApiTester $I, Example $example): void
    {
        $params = [
            'sortBy' => $I->generateListSortingOptions([
                'sort_key' => ListSortingOption::DEFAULT_SORT,
                'order_by' => ListSortingOption::DEFAULT_ORDER,
            ]),
            'page' => $example['page'],
            'filters' => [
                'steps_I_name' => [
                    'default' => [
                        'fieldId' => $this->stepNameFilterId,
                        'value' => $example['step'],
                    ],
                ],
                'fields_I_name' => [
                    'default' => [
                        'fieldId' => $this->fieldNameFilterId,
                        'value' => $example['field'],
                    ],
                ],
            ],
        ];

        // Given
        $I->getAnApiTokenFor('adminUser');

        $pagination = $example['pagination'] + [
            "page" => 1,
            "size" => 25,
            'sortBy' => 'base.dateEntered DESC',
            "total" => 30,
            "pages" => 2,
        ];

        // When
        $I->sendPOST("/Api/V8_Custom/List/{$this->listName}", $params);

        // Then
        $I->seeResponseIsDwpResponse(200);

        $responsePagination = $I->grabDataFromResponseByJsonPath('$.data.pagination')[0];

        if (!empty($example['email']) || !empty($example['group'])) {
            $I->assertCount($example['totalRows'], $I->grabDataFromResponseByJsonPath('$.data.rows')[0]);
        } else {
            unset($responsePagination['total']);
            unset($pagination['total']);
        }
        $I->assertEquals($pagination, $responsePagination);
    }

    protected function provider(): array
    {
        return [
            'no filter - page 1' => [
                'step' => '',
                'field' => '',
                'page' => 1,
                'pagination' => [],
            ],
            'no filter - page 2' => [
                'step' => '',
                'field' => '',
                'page' => 2,
                'pagination' => ['page' => 2],
            ],
            'filter on field name' => [
                'step' => 'ken',
                'field' => '',
                'page' => 1,
                'pagination' => ['total' => 15, 'pages' => 1],
                'totalRows' => 15,
            ],
            'filter on step name' => [
                'step' => '',
                'field' => 'GroupForRel01',
                'page' => 1,
                'pagination' => ['total' => 10, 'pages' => 1],
                'totalRows' => 10,
            ],
            'filter on field and step name' => [
                'step' => 'andreas',
                'field' => 'GroupForRel01',
                'page' => 1,
                'pagination' => ['total' => 4, 'pages' => 1],
                'totalRows' => 4,
            ],
        ];
    }
}
