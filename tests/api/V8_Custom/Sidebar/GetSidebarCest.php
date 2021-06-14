<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Sidebar;

use ApiTester;

class GetSidebarCest
{
    public function _before(ApiTester $I): void
    {
        $I->getAnApiTokenFor('adminUser');
    }

    public function createBlueSidebar(ApiTester $I): void
    {
        $userId = $I->generateUser('foo');

        $I->sendGET("/Api/V8_Custom/BlueSidebar/Users/$userId");
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);

        $assertPaths = [
            '$.data.some' => 'data',
        ];

        $I->seeAssertPathsInJson($assertPaths);
    }
}
