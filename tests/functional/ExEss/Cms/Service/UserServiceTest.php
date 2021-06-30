<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Service;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\Doctrine\Type\Locale;
use ExEss\Bundle\CmsBundle\Doctrine\Type\UserStatus;
use ExEss\Bundle\CmsBundle\Entity\User;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
use ExEss\Bundle\CmsBundle\Service\UserService;
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
            DataCleaner::jsonDecode(\json_encode($result))
        );
    }
}
