<?php declare(strict_types=1);

namespace Test\Api\Api\Login;

use ApiTester;
use Test\Api\CrudTestUser;

class FailureCest
{
    private CrudTestUser $user;

    public function _before(ApiTester $I): void
    {
        $this->user = new CrudTestUser($I);
    }

    public function verifyICantLogIn(ApiTester $I): void
    {
        // Given
        $I->haveHttpHeader('Content-Type', 'application/json;charset=utf-8');

        // When
        $I->sendPOST('/Api/login', \json_encode([
            'username' => $this->user->getUserName(),
            'password' => "not his password",
        ]));

        // Then
        $I->seeResponseIsDwpResponse(401);
    }
}
