<?php declare(strict_types=1);

namespace Test\Functional\Api\V8_Custom\Service\User;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Doctrine\Type\Locale;
use ExEss\Cms\Doctrine\Type\UserStatus;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Service\UserService;
use ExEss\Cms\Test\Testcase\FunctionalTestCase;

class UserServiceTest extends FunctionalTestCase
{
    private UserService $userService;

    private EntityManagerInterface $manager;

    public function _before(): void
    {
        $this->manager = $this->tester->grabService('doctrine.orm.entity_manager');
        $this->userService = $this->tester->grabService(UserService::class);
    }

    public function testGetDataForAdminUser(): void
    {
        // Given
        $userName = "blub@" . $this->tester->generateUuid() . ".com";
        $userId = $this->tester->generateUser($userName, [
            'status' => UserStatus::ACTIVE,
            'first_name' => 'B',
            'last_name' => 'D',
        ]);
        $this->tester->linkUserToRole($userId, User::ROLE_ADMIN);

        $user = $this->manager->find(User::class, $userId);

        // When
        $result = $this->userService->getDataFor($user);

        // Then
        $this->tester->assertEquals(
            [
                'user_name' => $userName,
                'last_name' => 'D',
                'first_name' => 'B',
                'full_name' => 'B D',
                'date_entered' => '2017-01-06 00:00:00',
                'email1' => $userName,
                'status' => UserStatus::ACTIVE,
                'is_admin' => true,
                'preferred_language' => Locale::EN,
            ],
            $result
        );
    }
}
