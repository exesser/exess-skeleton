<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Login;

use ApiTester;

class AuthenticateCest
{
    public function loginWithHttpHeader(ApiTester $I): void
    {
        // get a token and make sure it is not being sent with the cookies
        $token = $I->getAnApiTokenFor('adminUser');

        // login with token passed by a cookie
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST('/Api/V8_Custom/Flow/LEAD_CREATE', []);

        // assertions
        $I->seeResponseIsJson();
        $response = $I->grabResponse();
        $I->assertNotEquals('Authentication Error', $response);
    }
}
