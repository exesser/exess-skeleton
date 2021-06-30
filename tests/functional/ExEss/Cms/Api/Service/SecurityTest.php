<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Api\Service;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\Security;
use ExEss\Bundle\CmsBundle\Doctrine\Type\SecurityGroupType;
use ExEss\Bundle\CmsBundle\Entity\SecurityGroup;
use ExEss\Bundle\CmsBundle\Entity\User;
use Helper\Testcase\FunctionalTestCase;

class SecurityTest extends FunctionalTestCase
{
    private Security $security;

    public function _before(): void
    {
        $this->security = $this->tester->grabService(Security::class);
    }

    public function testCanGetPrimaryGroup(): void
    {
        $uid = $this->tester->generateUser('arne');
        $sid = $this->tester->generateSecurityGroup('some_primary_group', [
            'main_groups_c' => SecurityGroupType::EMPLOYEE
        ]);

        $this->tester->linkUserToSecurityGroup($uid, $sid, ['primary_group' => 1]);

        $this->tester->loginAsUser(
            $this->tester->grabEntityFromRepository(User::class, ['id' => $uid])
        );

        $this->tester->assertInstanceOf(
            SecurityGroup::class,
            $this->security->getPrimaryGroup(),
            'Primary group should be a security group'
        );

        $this->tester->assertEquals(
            $sid,
            $this->security->getPrimaryGroup()->getId(),
            'Fetched primary group should match previously created security group'
        );
    }
}
