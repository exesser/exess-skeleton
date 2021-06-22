<?php declare(strict_types=1);

namespace Test\Api\Api\Menu;

use ApiTester;

class GetMenuCest
{
    public function shouldReturn(ApiTester $I): void
    {
        // grab current items
        $items = $I->grabNumRecords('menu_mainmenu');

        // generate two extra
        $I->generateMenuMainMenu();
        $I->generateMenuMainMenu();

        // Given
        $I->getAnApiTokenFor('adminUser');

        $expectedItems = $items + 2;

        // When
        $I->sendGet('/Api/menu');

        // Then
        $I->seeResponseIsDwpResponse(200);
        $json = $I->grabDataFromResponseByJsonPath('$.data[*].name');

        $I->assertSame($expectedItems, \count($json));
    }
}
