<?php declare(strict_types=1);

namespace Test\Api\Api\ListDynamic;

use ApiTester;
use ExEss\Cms\Doctrine\Type\FilterFieldType;
use ExEss\Cms\Entity\ListCellLink;
use Helper\TestRepository;

class GetDefaultListCest
{
    private array $linkCells;
    private string $listName;

    public function _before(ApiTester $I): void
    {
        $this->linkCells = [];
        $dataListId = $I->generateDynamicList();
        for ($i = 1; $i <= 5; $i++) {
            $this->linkCells[] = $I->generateListLinkCell($dataListId, [
                'order_c' => $i,
                'date_entered' => \date('Y-m-d H:i:0') . $i,
                'cell_id' => $I->generateListCell(['line1' => "text line $i"]),
            ]);
        }

        // create a dynamic list
        $listId = $I->generateDynamicList([
            'name' => $this->listName = $I->generateUuid(),
            'date_entered' => '2017-01-06 00:00:00',
            'display_footer' => true,
            'base_object' => ListCellLink::class,
            'filters_have_changed' => 1,
            'items_per_page' => 20,
            'combined' => 0,
        ]);

        $I->generateListCellForList($listId, [
            'name' => 'blub',
            'date_entered' => '2017-01-06 00:00:00',
            'type' => 'list_simple_two_liner_cell',
            'line1' => '%cell|line1%',
        ], [
            'date_entered' => '2017-01-06 00:00:00',
        ]);
    }

    public function getInternalList(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPOST("/Api/list/$this->listName");

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.rows[0].id' => $this->linkCells[4],
            '$.data.rows[1].id' => $this->linkCells[3],
            '$.data.rows[2].id' => $this->linkCells[2],
            '$.data.rows[3].id' => $this->linkCells[1],
            '$.data.rows[4].id' => $this->linkCells[0],
            '$.data.rows[0].cells[0].options.line1' => 'text line 5',
        ]);
    }

    public function getExternalList(ApiTester $I): void
    {
        // Given
        $dynamicListId = $I->generateDynamicList([
            'name' => $listName = $I->generateUuid(),
            'date_entered' => '2017-01-06 00:00:00',
            'date_modified' => '2017-01-06 00:00:00',
            'modified_user_id' => '1',
            'display_footer' => true,
            'title' => 'My List Title',
            'filters_have_changed' => 1,
            'items_per_page' => 20,
            'combined' => 0,
            'external_object_id' => $I->generateListExternalObject(['class_handler' => TestRepository::NAME]),
        ]);

        $I->generateListCellForList($dynamicListId, [
            'type' => 'list_simple_two_liner_cell',
            'line1' => '%billingAccount% - %lastRejectReason%',
        ]);

        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPOST(
            "/Api/list/$listName",
            ['extraActionData' => ['name' => 'Ken Block', 'number' => '43']]
        );

        // Then
        $I->seeResponseIsDwpResponse(200);

        $row = $I->grabDataFromResponseByJsonPath('$.data.rows[0]')[0];
        unset($row['createDate']);

        $I->assertArrayEqual(
            $I->loadJsonWithParams(__DIR__ . '/resources/list-external-rows.json'),
            $row
        );

        $I->assertArrayEqual(
            $I->loadJsonWithParams(__DIR__ . '/resources/list-settings.json'),
            $I->grabDataFromResponseByJsonPath('$.data.settings')[0]
        );
    }

    public function filterInternalList(ApiTester $I): void
    {
        // Given
        $listId = $I->generateDynamicList([
            'name' => $listName = $I->generateUuid(),
            'date_entered' => '2017-01-06 00:00:00',
            'display_footer' => true,
            'base_object' => ListCellLink::class,
            'filters_have_changed' => 1,
            'items_per_page' => 20,
            'combined' => 0,
            'filter_id' => $filterId = $I->generateFilter(),
        ]);
        $I->generateListCellForList($listId, [
            'name' => 'blub',
            'date_entered' => '2017-01-06 00:00:00',
            'type' => 'list_simple_two_liner_cell',
            'line1' => '%cell|line1%',
        ], [
            'date_entered' => '2017-01-06 00:00:00',
        ]);
        $I->linkFilterToFieldGroup(
            $filterId,
            $filterFieldGroupId = $I->generateFilterFieldGroup()
        );
        $I->linkFilterFieldToFieldGroup(
            $filterFieldId = $I->generateFilterField([
                'operator' => 'SQL',
                'field_key_c' => 'cell|line1',
                'type_c' => FilterFieldType::SELECT_WITH_SEARCH,
                'field_sql_c' => "LIKE '####%'",
            ]),
            $filterFieldGroupId
        );

        // When
        $I->getAnApiTokenFor('adminUser');
        $I->sendPOST(
            "/Api/list/$listName",
            [
                'page' => 1,
                'filters' => [
                    'cell_I_line1' => [
                        'default' => [
                            'fieldId' => $filterFieldId,
                            'value' => [
                                [
                                    'key' => "text line 3",
                                    'label' => "does not matter",
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.rows[0].id' => $this->linkCells[2],
        ]);
    }
}
