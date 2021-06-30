<?php declare(strict_types=1);

namespace Test\Api\Api\Login;

use ApiTester;
use ExEss\Bundle\CmsBundle\Entity\UserLogin;
use Test\Api\V8_Custom\Crud\CrudTestUser;

class SuccessCest
{
    private CrudTestUser $user;

    public function _before(ApiTester $I): void
    {
        $this->user = new CrudTestUser($I);
    }

    public function verifyIGetAnApiToken(ApiTester $I): void
    {
        // Given
        $I->haveHttpHeader('Content-Type', 'application/json;charset=utf-8');

        // When
        $token = $I->getAnApiTokenFor($this->user->getUserName(), $this->user->getPassword());

        // Then
        $I->flushToDatabase();
        $I->assertNotEmpty($token);

        $lastLogin = $I->grabFromRepository(UserLogin::class, 'lastLogin', ['id' => $this->user->getId()]);
        $I->assertEquals((new \DateTime())->format('Y-m-d'), (new \DateTime($lastLogin))->format('Y-m-d'));

        $jwt = $I->grabFromRepository(UserLogin::class, 'jwt', ['id' => $this->user->getId()]);
        $I->assertNotEmpty($jwt);
        //$I->assertEquals($token, $jwt);
    }
}
