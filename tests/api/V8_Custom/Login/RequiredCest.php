<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Login;

use ApiTester;

class RequiredCest
{
    public function anAuthenticationErrorShouldBeRaised(ApiTester $I): void
    {
        $I->sendPOST('/Api/V8_Custom/Flow/LEAD_CREATE');

        // assertions
        $I->seeResponseCodeIs(401);
        $I->seeResponseEquals('{"message":"Authentication Required"}');
    }
}
