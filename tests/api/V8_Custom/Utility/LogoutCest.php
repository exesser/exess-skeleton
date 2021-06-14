<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Utility;

use ApiTester;

class LogoutCest
{
    public function shouldReturn(ApiTester $I): void
    {
        $I->getAnApiTokenFor('adminUser');

        $I->sendGet('/Api/logout');

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}
