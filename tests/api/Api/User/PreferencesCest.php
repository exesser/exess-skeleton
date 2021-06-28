<?php declare(strict_types=1);

namespace Test\Api\Api\User;

use ApiTester;
use ExEss\Cms\Doctrine\Type\Locale;
use ExEss\Cms\Entity\User;
use Test\Api\V8_Custom\Crud\CrudTestUser;

class PreferencesCest
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
        $I->sendGet('/Api/user/preferences');

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.preferredLanguage' => $I->grabFromDatabase('users', 'preferred_locale', ['id' => '1']),
        ]);
    }

    public function shouldReturnCrudUserData(ApiTester $I): void
    {
        // Given
        $userName = $this->user->getUserName();
        $I->getAnApiTokenFor($userName, $this->user->getPassword());

        // When
        $I->sendGet('/Api/user/preferences');

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.preferredLanguage' => Locale::EN,
        ]);
    }

    public function shouldReturnRecoveryData(ApiTester $I): void
    {
        // Given
        $userName = $this->user->getUserName();
        $I->linkUserToRole($this->user->getId(), User::ROLE_ADMIN);
        $I->generateUserGuidanceRecovery($this->user->getId(), ["linkTo" => "flowId", "params" => ["p1"=>"v1"]]);
        $I->getAnApiTokenFor($userName, $this->user->getPassword());

        // When
        $I->sendGet('/Api/user/preferences');

        // Then
        $I->seeResponseIsDwpResponse(200);
        $I->seeAssertPathsInJson([
            '$.data.preferredLanguage' => Locale::EN,
            '$.data.command' => [
                "command" => "navigate",
                "arguments" => [
                    "linkTo" => "flowId",
                    "params" => [
                        "p1" => "v1"
                    ]
                ],
                "relatedBeans" => [],
                "params" => [],
                "confirmTitle" => "Guidance recovery",
                "confirmMessage" => "You have unfinished guidance, do you want to recover it?"
            ],
        ]);
    }

    public function shouldFail(ApiTester $I): void
    {
        // Given
        $I->haveHttpHeader('Content-Type', 'application/json;charset=utf-8');

        // When
        $I->sendGet('/Api/user/preferences');

        // Then
        $I->seeResponseIsDwpResponse(401);
    }
}
