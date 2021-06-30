<?php declare(strict_types=1);

namespace Test\Api\Api\ListDynamic;

use ApiTester;
use ExEss\Bundle\CmsBundle\Service\GridService;

class RowGridCest
{
    private string $userId;
    private string $listName;

    public function _before(ApiTester $I): void
    {
        $this->userId = $I->generateUser('boo ba');
        $I->generateDynamicList([
            'name' => $this->listName = $I->generateUuid(),
        ]);
    }

    public function shouldReturnDefaultGrid(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');
        $gridKey = GridService::DEFAULT_ACTION_BAR_GRID;

        // When
        $I->sendPost("/Api/list/$this->listName/row/grid/$gridKey/$this->userId", [
            'actionData' => ['foo' => 'bar'],
        ]);

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->assertArrayEqual(
            $I->loadJsonWithParams(
                __DIR__ . '/resources/row-grid-action-bar.json',
                [
                    'recordId' => $this->userId,
                    'recordType' => $this->listName,
                ]
            ),
            $I->grabDataFromResponseByJsonPath('$.data.grid')[0]
        );
    }

    public function shouldReturnConfiguredGrid(ApiTester $I): void
    {
        // Given
        $parentId = $I->generateUuid();
        $I->generateGrid([
            'key_c' => $gridKey = $I->generateUuid(),
            'json_fields_c' => \json_encode($I->loadJsonWithParams(
                __DIR__ . '/resources/row-grid-external.json',
                [
                    'parentId' => '%parentId%',
                    'recordId' => '%recordId%',
                ]
            ))
        ]);
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendPost("/Api/list/$this->listName/row/grid/$gridKey/$this->userId", [
            'actionData' => ['parentId' => $parentId]
        ]);

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->assertArrayEqual(
            $I->loadJsonWithParams(
                __DIR__ . '/resources/row-grid-external.json',
                [
                    'recordId' => $this->userId,
                    'parentId' => $parentId,
                ]
            ),
            $I->grabDataFromResponseByJsonPath('$.data.grid')[0]
        );
    }
}
