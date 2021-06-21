<?php declare(strict_types=1);

namespace Test\Api\Api\Check;

use ApiTester;
use Test\Api\V8_Custom\Crud\CrudTestUser;

class PingCest
{
    private CrudTestUser $user;

    public function _before(ApiTester $I): void
    {
        $this->user = new CrudTestUser($I);
        $this->user->linkSecurity($I->generateSecurityGroup('third party'));
    }

    public function pingCheckSucceeds(ApiTester $I): void
    {
        // Given
        $this->user->login();

        // When
        $I->sendGet('/Api/check/ping');

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.result' => true,
        ]);
    }
}
