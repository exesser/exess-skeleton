<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\ChangeLocale;

use ApiTester;
use ExEss\Cms\Entity\User;

class ChangeLocaleCest
{
    public function shouldChangeLocale(ApiTester $I): void
    {
        // setup
        $token = $I->getAnApiTokenFor('adminUser');

        // run test
        $I->sendPOST('/Api/V8_Custom/user/change-locale/fr_BE');

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeInRepository(User::class, ['id' => 1, 'preferredLocale' => 'fr_BE']);

        // run test
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST('/Api/V8_Custom/user/change-locale/en_BE');

        // assertions
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeInRepository(User::class, ['id' => 1, 'preferredLocale' => 'en_BE']);
    }

    public function shouldFailWhenWrongLocaleSend(ApiTester $I): void
    {
        // setup
        $I->getAnApiTokenFor('adminUser');

        // run test
        $I->sendPOST('/Api/V8_Custom/user/change-locale/something');

        // assertions
        $I->seeResponseCodeIs(400);
    }
}
