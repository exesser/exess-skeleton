<?php declare(strict_types=1);

namespace Test\Api\V8_Custom\Security;

use ApiTester;
use Codeception\Example;
use ExEss\Cms\Doctrine\Type\SecurityGroupType;
use ExEss\Cms\Doctrine\Type\UserStatus;
use ExEss\Cms\Entity\User;
use Slim\Http\Response;
use ExEss\Cms\Api\V8_Custom\Controller\CrudController;

class SecurityEndpointCest
{
    private const USERNAME = 'cool@user.com';
    private const PASSWORD = 'CoolUser';
    private const TEST_ROUTE = '/V8_Custom/Menu';

    private array $currentRecords;
    private string $userId;

    public function _before(ApiTester $I): void
    {
        $this->currentRecords = $I->grabAllFromDatabase('securitygroups_api');
        $I->deleteFromDatabase('securitygroups_api');

        $controllerMock = \Mockery::mock(CrudController::class);
        $controllerMock->shouldReceive('getRecordsInformation')->andReturn(new Response(200));

        $I->mockService(CrudController::class, $controllerMock);

        $this->userId = $I->generateUser(self::USERNAME, [
            'salt' => $salt = (new User)->getSalt(),
            'user_hash' => User::getPasswordHash(self::PASSWORD, $salt),
            'status' => UserStatus::ACTIVE,
        ]);
        $I->linkUserToRole($this->userId, 'ROLE_USER');
    }

    public function _after(ApiTester $I): void
    {
        $I->restoreInDatabase('securitygroups_api', $this->currentRecords);
    }

    public function testWithNoGroups(ApiTester $I): void
    {
        // Given
        $I->getAnApiTokenFor(self::USERNAME, self::PASSWORD);

        // When
        $I->sendGet('/Api' . self::TEST_ROUTE);

        // Then
        $I->seeResponseCodeIs(403);
    }

    protected function expectationProvider(): array
    {
        return [
            [
                "userGroups" => "^WHAT-EVA^,^EMPLOYEE^",
                "code" => 200,
            ],
            [
                "userGroups" => "^WHAT-EVA^",
                "code" => 403,
            ],
        ];
    }

    /**
     * @dataProvider expectationProvider
     */
    public function testOnlyWithUserGroups(ApiTester $I, Example $example): void
    {
        // Given
        $I->linkUserToSecurityGroup(
            $this->userId,
            $I->generateSecurityGroup(
                'my exesser',
                ['main_groups_c' => SecurityGroupType::EMPLOYEE]
            )
        );
        $I->generateSecurityApiRecord('GET', self::TEST_ROUTE, $example['userGroups']);
        $I->getAnApiTokenFor(self::USERNAME, self::PASSWORD);

        // When
        $I->sendGet('/Api' . self::TEST_ROUTE);

        // Then
        $I->seeResponseCodeIs($example['code']);
    }
}
