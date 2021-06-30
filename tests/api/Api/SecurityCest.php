<?php declare(strict_types=1);

namespace Test\Api\Api;

use ApiTester;
use Codeception\Example;
use ExEss\Bundle\CmsBundle\Doctrine\Type\HttpMethod;
use ExEss\Bundle\CmsBundle\Doctrine\Type\SecurityGroupType;
use Test\Api\V8_Custom\Crud\CrudTestUser;

class SecurityCest
{
    private array $currentRecords;
    private CrudTestUser $user;

    public function _before(ApiTester $I): void
    {
        $this->currentRecords = $I->grabAllFromDatabase('securitygroups_api');
        $I->deleteFromDatabase('securitygroups_api');

        $this->user = new CrudTestUser($I);
    }

    public function _after(ApiTester $I): void
    {
        $I->restoreInDatabase('securitygroups_api', $this->currentRecords);
    }

    public function testWithNoGroups(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor($this->user->getUserName(), $this->user->getPassword());

        // When
        $I->sendGet('/Api/menu');

        // Then
        $I->seeResponseCodeIs(403);
    }

    public function testWithAdmin(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor('adminUser');

        // When
        $I->sendGet('/Api/menu');

        // Then
        $I->seeResponseCodeIs(200);
    }

    protected function groupTypeProvider(): array
    {
        return [
            [
                "userGroups" => "^WHAT-EVA^,^" . SecurityGroupType::EMPLOYEE . "^",
                "code" => 200,
            ],
            [
                "userGroups" => "^WHAT-EVA^",
                "code" => 403,
            ],
        ];
    }

    /**
     * @dataProvider groupTypeProvider
     */
    public function testOnlyWithUserGroups(ApiTester $I, Example $example): void
    {
        // Given
        $I->linkUserToSecurityGroup(
            $this->user->getId(),
            $I->generateSecurityGroup(
                'my exesser',
                ['main_groups_c' => SecurityGroupType::EMPLOYEE]
            )
        );
        $I->generateSecurityApiRecord(HttpMethod::GET, 'exess_cms_menu_main__invoke', $example['userGroups']);
        $I->getAnApiTokenFor($this->user->getUserName(), $this->user->getPassword());

        // When
        $I->sendGet('/Api/menu');

        // Then
        $I->seeResponseCodeIs($example['code']);
    }
}
