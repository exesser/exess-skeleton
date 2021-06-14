<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\FatEntity;

use ExEss\Cms\Acl\AclService;
use ExEss\Cms\Doctrine\Type\UserStatus;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Test\Testcase\FunctionalTestCase;

class SecurityGroupACLTest extends FunctionalTestCase
{
    private string $userId;

    public function _before(): void
    {
        $this->userId =
            $this->tester->haveInRepository(User::class, ['status' => UserStatus::ACTIVE, 'createdBy' => '1']);
    }

    /**
     * @skip TODO move away from the entity manager
     */
    public function testGroupDefaultDeny(): void
    {
        // Given
        $id = $this->tester->generateDynamicList();
        $group = $this->tester->generateSecurityGroup('some-group');

        $this->tester->grantsAclRightsTo(
            $this->userId,
            \LIST_dynamic_list::class,
            'list',
            AclService::ACL_ALLOW_GROUP
        );

        $this->tester->loginAsUser($this->tester->grabEntityFromRepository(User::class, ['id' => $this->userId]));

        // When
        $entity = $this->fatEntityManager->findFatEntity(\LIST_dynamic_list::class, ['id' => Expr::eq($id)]);

        // Then
        $this->tester->assertNull(
            $entity,
            'The entity should not be found, since this new entity has no security groups and the ACL is default deny'
        );

        // Given
        $this->assignSecurityGroup($group, $this->userId, $id);

        // When
        $entity = $this->fatEntityManager->findFatEntity(\LIST_dynamic_list::class, ['id' => Expr::eq($id)]);

        // Then
        $this->tester->assertInstanceOf(
            \LIST_dynamic_list::class,
            $entity,
            'The entity should be found, since now a security group is assigned'
        );
    }

    /**
     * @skip TODO move away from the entity manager
     */
    public function testGroupDefaultAllow(): void
    {
        $id = $this->tester->generateDynamicList();
        $group = $this->tester->generateSecurityGroup('some-group');

        $this->tester->grantsAclRightsTo(
            $this->userId,
            \LIST_dynamic_list::class,
            'list',
            AclService::ACL_ALLOW_GROUP_DEFAULT_ALLOW
        );

        $this->tester->loginAsUser($this->tester->grabEntityFromRepository(User::class, ['id' => $this->userId]));

        $fatEntity = $this->fatEntityManager->findFatEntity(\LIST_dynamic_list::class, ['id' => Expr::eq($id)]);

        $this->tester->assertInstanceOf(
            \LIST_dynamic_list::class,
            $fatEntity,
            'The entity should be found, since this new entity has no security groups and the ACL is default allow'
        );

        // assign security group record to different user, so the product should not be found anymore..
        $this->assignSecurityGroup($group, "1", $id);

        $fatEntity = $this->fatEntityManager->findFatEntity(LIST_dynamic_list::class, ['id' => Expr::eq($id)]);

        $this->tester->assertNull(
            $fatEntity,
            'The entity should not be found anymore, since at least one group is assigned'
        );
    }

    private function assignSecurityGroup(string $groupId, string $userId, string $listId): void
    {
        $this->tester->linkSecurityGroupList($listId, $groupId);
        $this->tester->generateSecurityGroupUsers($groupId, $userId);
    }
}
