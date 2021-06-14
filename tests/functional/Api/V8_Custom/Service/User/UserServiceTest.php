<?php declare(strict_types=1);

namespace Test\Functional\Api\V8_Custom\Service\User;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Api\V8_Custom\Service\User\UserService;
use ExEss\Cms\Doctrine\Type\UserStatus;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Test\Testcase\FunctionalTestCase;

class UserServiceTest extends FunctionalTestCase
{
    private const TEST_USER_USERNAME = 'skrstic';
    private const TEST_USER_PASSWORD = 'qwerty';

    private UserService $userService;

    private EntityManagerInterface $manager;

    public function _before(): void
    {
        $this->manager = $this->tester->grabService('doctrine.orm.entity_manager');
        $this->userService = $this->tester->grabService(UserService::class);
    }

    public function testGetDataForAdminUser(): void
    {
        // assign - User and Account
        $userId = $this->tester->generateUser(static::TEST_USER_USERNAME, [
            'salt' => $salt = (new User)->getSalt(),
            'user_hash' => User::getPasswordHash(static::TEST_USER_PASSWORD, $salt),
            'status' => UserStatus::ACTIVE,
            'user_name' => 'blub@blub.com',
        ]);
        $this->tester->linkUserToRole($userId, User::ROLE_ADMIN);

        $user = $this->manager->find(User::class, $userId);

        // act
        $result = $this->userService->getData($user);

        // assert
        $this->tester->assertEquals($user->isAdmin(), $result['is_admin']);
    }
}
