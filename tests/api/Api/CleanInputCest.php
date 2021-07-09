<?php declare(strict_types=1);

namespace Test\Api\Api;

use ApiTester;
use Test\Api\CrudTestUser;

class CleanInputCest
{
    private CrudTestUser $user;

    public function _before(ApiTester $I): void
    {
        $this->user = new CrudTestUser($I);
    }

    public function testInputCleaning(ApiTester $I): void
    {
        // Given
        $password = $this->user->getPassword();

        // When
        $token = $I->getAnApiTokenFor($this->user->getUserName(), '<script type="foo">bar</script>' . $password);

        // Then
        // the fact we get logged in, means the bad content was stripped out during the request
        $I->assertNotEmpty($token, "the script tags were not stripped out");
    }
}
