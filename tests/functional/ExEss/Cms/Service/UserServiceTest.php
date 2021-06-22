<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Service;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Doctrine\Type\Locale;
use ExEss\Cms\Doctrine\Type\UserStatus;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Service\UserService;
use Helper\Testcase\FunctionalTestCase;

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
        $result = $this->userService->getPreferencesData($user);

        // Then
        $this->tester->assertEquals(
            [
                'preferredLanguage' => Locale::EN,
            ],
            \json_decode(\json_encode($result), true)
        );
    }
}
