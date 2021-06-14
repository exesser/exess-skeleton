<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\EventListener;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Test\Testcase\FunctionalTestCase;

/**
 * @see DateModifiedListener
 */
class DateModifiedListenerTest extends FunctionalTestCase
{
    private EntityManager $em;

    public function _before(): void
    {
        $this->em = $this->tester->grabService('doctrine.orm.entity_manager');
    }

    public function testDateModified(): void
    {
        // Given
        $user = new User();
        $user->setCreatedBy('1');
        $user->setDateEntered(new \DateTimeImmutable);

        // When
        $this->em->persist($user);
        $this->em->flush();

        // Then
        $this->tester->assertEquals(null, $user->getDateModified());

        // Given
        $user->setLastName('foo');

        // When
        $this->em->persist($user);
        $this->em->flush();

        // Then
        $this->tester->assertAlmostNow($user->getDateModified());
    }
}
