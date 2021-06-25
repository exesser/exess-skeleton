<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\EventListener;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Doctrine\Type\UserStatus;
use ExEss\Cms\Entity\AclRole;
use ExEss\Cms\Entity\User;
use Helper\Testcase\FunctionalTestCase;

/**
 * @see DefaultRoleListener
 */
class DefaultRoleListenerTest extends FunctionalTestCase
{
    private EntityManager $em;

    public function _before(): void
    {
        $this->em = $this->tester->grabService('doctrine.orm.entity_manager');
    }

    public function testAddNewUser(): void
    {
        // Given
        $user = new User();
        $user->setUserName("UserName");
        $user->setUserHash('$1$ziAPwUQV$HZeM19ckAW9gcAl6Lp7dR0');
        $user->setFirstName('First');
        $user->setLastName('Last');
        $user->setStatus(UserStatus::ACTIVE);
        $user->setEmployeeStatus('Active');

        // When
        $this->em->persist($user);

        // Then
        $this->assertNotEmpty($user->getRoles());
        $this->assertTrue($user->hasRole(AclRole::DEFAULT_ROLE_CODE));
    }
}
