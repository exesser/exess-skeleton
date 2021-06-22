<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Lists;

use ApiTester;
use Ramsey\Uuid\Uuid;

class GetListExtraRowContentCest
{
    private string $userId;

    public function _before(ApiTester $I): void
    {
        $this->userId = $I->generateUser('boo ba');
    }

    public function shouldReturn(ApiTester $I): void
    {
        // When
        $I->getAnApiTokenFor('adminUser');
        $I->sendPOST('/Api/V8_Custom/ListExtraRowContent/action-bar/Users/' . $this->userId);

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.grid.columns[0].rows[0].options.recordType' => 'Users',
            '$.data.grid.columns[0].rows[0].options.recordId' => $this->userId,
            '$.data.grid.columns[0].rows[0].options.gridKey' => 'action-bar',
        ]);
    }

    public function shouldReturnConfiguredGrid(ApiTester $I): void
    {
        // Given
        $parentId = Uuid::uuid4()->toString();
        $gridKey = 'grid-test-key';

        $I->generateGrid([
            'key_c' => $gridKey,
            'json_fields_c' => \json_encode($I->loadJsonWithParams(
                __DIR__ . '/resources/list-external-rows-grid.json',
                [
                    'parentId' => '%parentId%',
                    'recordId' => '%recordId%',
                ]
            ))
        ]);

        // When
        $I->getAnApiTokenFor('adminUser');
        $I->sendPOST("/Api/V8_Custom/ListExtraRowContent/$gridKey/Users/" . $this->userId, [
            'actionData' => ['parentId' => $parentId]
        ]);

        // Then
        $I->seeResponseIsDwpResponse(200);
        $grid = $I->grabDataFromResponseByJsonPath('$.data.grid')[0];

        $I->assertArrayEqual(
            $I->loadJsonWithParams(__DIR__ . '/resources/list-external-rows-grid.json', [
                'recordId' => $this->userId,
                'parentId' => $parentId,
            ]),
            $grid
        );
    }
}
