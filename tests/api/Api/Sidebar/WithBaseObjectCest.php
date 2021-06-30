<?php declare(strict_types=1);

namespace Test\Api\Api\Sidebar;

use ApiTester;
use ExEss\Bundle\CmsBundle\Entity\User;

class WithBaseObjectCest
{
    public function createBlueSidebar(ApiTester $I): void
    {
        // Given
        $userId = $I->generateUser('foo');
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendGET("/Api/sidebar/" . \urlencode(User::class) . "/$userId");

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.some' => 'data',
        ]);
    }
}
