<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Menu;

use ApiTester;

class GetSubMenuCest
{
    public function shouldReturn(ApiTester $I): void
    {
        // Given
        $menuId = $I->generateMenuMainMenu([
            'name' => $name = $I->generateUuid(),
        ]);
        $I->linkMenuMainMenuToDashboard(
            $menuId,
            $I->generateDashboard()
        );
        $I->linkMenuMainMenuToDashboard(
            $menuId,
            $I->generateDashboard()
        );
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendGET("/Api/V8_Custom/Menu/$name");

        // Then
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->assertCount(
            2,
            $I->grabDataFromResponseByJsonPath('$.data[*].label'),
        );
    }
}
