<?php declare(strict_types=1);

namespace Test\Api\Api\Menu;

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
        $I->sendGET("/Api/menu/$name");

        // Then
        $I->seeResponseIsDwpResponse(200);

        $I->assertCount(
            2,
            $I->grabDataFromResponseByJsonPath('$.data[*].label'),
        );
    }
}
