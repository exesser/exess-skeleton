<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Dashboard;

use ApiTester;
use ExEss\Cms\Entity\Dashboard;
use ExEss\Cms\Entity\User;

class GetDashboardWithNameCest
{
    public function shouldReturn(ApiTester $I): void
    {
        // Given
        $user = $I->grabEntityFromRepository(User::class, ['id' => '1']);
        $I->haveInRepository(Dashboard::class, [
            'createdBy' => $user,
            'dateEntered' => new \DateTime(),
            'key' => $key = $I->generateUuid(),
            'name' => $name = $I->generateUuid(),
            'gridTemplate' => [
                'createdBy' => $user,
                'dateEntered' => new \DateTime(),
            ],
        ]);

        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendGET("/Api/V8_Custom/Dashboard/$key");

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.title' => $name,
        ]);
    }
}
