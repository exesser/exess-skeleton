<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Utility;

use ApiTester;

class GetCurrentUserCest
{
    public function shouldWork(ApiTester $I): void
    {
        // setup
        $I->getAnApiTokenFor('adminUser');

        $I->sendGET('/Api/V8_Custom/user/current');

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $userName = $I->grabFromDatabase('users', 'user_name', ['id' => '1']);
        $assertPaths = [
            '$.data.user_name' => $userName,
            '$.data.is_admin' => true
        ];

        $I->seeAssertPathsInJson($assertPaths);
    }

    public function shouldWorkWithGuidanceRecovery(ApiTester $I): void
    {
        $I->generateUserGuidanceRecovery('1', '{"data": "recoveryData"}');

        // setup
        $I->getAnApiTokenFor('adminUser');

        $I->sendGET('/Api/V8_Custom/user/current');

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $userName = $I->grabFromDatabase('users', 'user_name', ['id' => '1']);
        $assertPaths = [
            '$.data.user_name' => $userName,
            '$.data.is_admin' => true,
            // @todo add this modal to CRUD
            //'$.data.command.arguments.flowId' => 'gf_ask_guidance_recovery_modal'
        ];

        $I->seeAssertPathsInJson($assertPaths);
    }

    public function shouldFail(ApiTester $I): void
    {
        $I->sendGET('/Api/V8_Custom/user/current');

        // assertions
        $I->seeResponseCodeIs(401);
    }
}
