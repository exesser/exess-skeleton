<?php declare(strict_types=1);

namespace Test\Api\Api\User;

use ApiTester;
use Test\Api\V8_Custom\Crud\CrudTestUser;

class CurrentCest
{
    private CrudTestUser $user;

    public function _before(ApiTester $I): void
    {
        $this->user = new CrudTestUser($I);
    }

    public function shouldReturnAdminUserData(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendGET('/Api/user/current');

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.user_name' => $I->grabFromDatabase('users', 'user_name', ['id' => '1']),
            '$.data.is_admin' => true,
        ]);
    }

    public function shouldReturnCrudUserData(ApiTester $I): void
    {
        // Given
        $userName = $this->user->getUserName();
        $I->getAnApiTokenFor($userName, $this->user->getPassword());

        // When
        $I->sendGET('/Api/user/current');

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.user_name' => $userName,
            '$.data.is_admin' => false,
        ]);
    }

    public function shouldReturnRecoveryData(ApiTester $I): void
    {
        // Given
        $userName = $this->user->getUserName();
        $I->generateUserGuidanceRecovery($this->user->getId(), ["data" => "recoveryData"]);
        $I->getAnApiTokenFor($userName, $this->user->getPassword());

        // When
        $I->sendGET('/Api/user/current');

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.user_name' => $userName,
            '$.data.is_admin' => false,
            // @todo add this modal to CRUD
            //'$.data.command.arguments.flowId' => 'gf_ask_guidance_recovery_modal',
        ]);
    }

    public function shouldFail(ApiTester $I): void
    {
        // Given
        $I->haveHttpHeader('Content-Type', 'application/json;charset=utf-8');

        // When
        $I->sendGET('/Api/user/current');

        // Then
        $I->seeResponseIsDwpResponse(401);
    }
}