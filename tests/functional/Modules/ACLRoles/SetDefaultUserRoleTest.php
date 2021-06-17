<?php declare(strict_types=1);

namespace Test\Functional\Modules\ACLRoles;

use ExEss\Cms\Doctrine\Type\UserStatus;
use ExEss\Cms\Entity\AclRole;
use ExEss\Cms\Entity\User;
use Helper\Testcase\FunctionalTestCase;

class SetDefaultUserRoleTest extends FunctionalTestCase
{
    /**
     * @see DefaultRoleListener
     */
    public function testAddNewUser(): void
    {
        $user = new User();
        $user->setUserName("UserName");
        $user->setUserHash('$1$ziAPwUQV$HZeM19ckAW9gcAl6Lp7dR0');
        $user->setFirstName('First');
        $user->setLastName('Last');
        $user->setStatus(UserStatus::ACTIVE);
        $user->setEmployeeStatus('Active');

        $this->tester->grabService('doctrine.orm.entity_manager')->persist($user);

        $this->assertNotEmpty($user->getRoles());
        $this->assertTrue($user->hasRole(AclRole::DEFAULT_ROLE_CODE));
    }
}
