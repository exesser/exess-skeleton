<?php declare(strict_types=1);

namespace Test\Api\Api\Login;

use ApiTester;

class RequiredCest
{
    public function anAuthenticationErrorShouldBeRaised(ApiTester $I): void
    {
        // When
        $I->sendPOST('/Api/user/preferences');

        // Then
        $I->seeResponseIsDwpResponse(401);
        $I->seeResponseEquals('{"status":401,"data":{"message":"Unauthorized"},"message":"ERROR"}');
    }
}
