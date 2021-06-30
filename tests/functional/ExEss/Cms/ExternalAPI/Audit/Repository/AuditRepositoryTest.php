<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\ExternalAPI\Audit\Repository;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\AuditRepository;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\Response\AuditList;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\Response\AuditRow;
use ExEss\Bundle\CmsBundle\Dictionary\Format;
use ExEss\Bundle\CmsBundle\Doctrine\Type\Locale;
use ExEss\Bundle\CmsBundle\Doctrine\Type\UserStatus;
use ExEss\Bundle\CmsBundle\Entity\User;
use Helper\Testcase\FunctionalTestCase;

class AuditRepositoryTest extends FunctionalTestCase
{
    protected AuditRepository $repository;

    public function _before(): void
    {
        $this->repository = $this->tester->grabService(AuditRepository::class);
    }

    public function testFindBy(): void
    {
        // Given
        $userId = $this->createAndUpdateUser();
        $this->createAndUpdateUser();
        $this->createAndUpdateUser();

        // When
        /** @var AuditList $response */
        $response = $this->repository->findBy([
            'recordId' => $userId,
            'recordType' => User::class,
            'page' => 1,
            'limit' => 10,
        ]);

        // Then
        $this->tester->assertInstanceOf(AuditList::class, $response);
        $this->tester->assertEquals(3, $response->getPagination()->getTotal());

        $audit = $response->getList()[0];
        $this->tester->assertInstanceOf(AuditRow::class, $audit);
        $this->tester->assertEquals('UPDATE', $audit->getOperation());
        $this->tester->assertEquals(User::USERNAME_ADMIN, $audit->getUsername());
        $changes = \explode('<br>', $audit->getChanges());
        $this->tester->assertCount(2, $changes);
        $this->tester->assertEquals('<b>preferred_locale</b>(varchar): <i>empty</i> -> en_BE', $changes[0]);

        $audit = $response->getList()[1];
        $this->tester->assertInstanceOf(AuditRow::class, $audit);
        $this->tester->assertEquals('UPDATE', $audit->getOperation());
        $this->tester->assertEquals(null, $audit->getUsername());
        $changes = \explode('<br>', $audit->getChanges());
        $this->tester->assertCount(3, $changes);
        $this->tester->assertEquals('<b>status</b>(varchar): Active -> Inactive', $changes[0]);
        $this->tester->assertEquals('<b>preferred_locale</b>(varchar): nl_BE -> <i>empty</i>', $changes[1]);

        $audit = $response->getList()[2];
        $this->tester->assertInstanceOf(AuditRow::class, $audit);
        $this->tester->assertEquals('INSERT', $audit->getOperation());
        $this->tester->assertEquals(User::USERNAME_ADMIN, $audit->getUsername());
    }

    private function createAndUpdateUser(): string
    {
        $userId = $this->tester->generateUser('user name', [
            'preferred_locale' => Locale::NL,
            'status' => UserStatus::ACTIVE,
            'date_entered' => \date(Format::DB_DATETIME_FORMAT),
            'date_modified' => \date(Format::DB_DATETIME_FORMAT),
        ]);
        $this->tester->updateInDatabase(
            'users',
            [
                'preferred_locale' => null,
                'status' => UserStatus::INACTIVE,
            ],
            ['id' => $userId]
        );
        $this->tester->updateInDatabase(
            'users',
            [
                'modified_user_id' => "1",
                'preferred_locale' =>  Locale::EN,
            ],
            ['id' => $userId]
        );

        return $userId;
    }
}
