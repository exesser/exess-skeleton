<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\EventListener;

use Doctrine\ORM\EntityManager;
use ExEss\Bundle\CmsBundle\Entity\User;
use Helper\Testcase\FunctionalTestCase;

/**
 * @see HtmlPurifierListener
 */
class HtmlPurifierListenerTest extends FunctionalTestCase
{
    private EntityManager $em;

    public function _before(): void
    {
        $this->em = $this->tester->grabService('doctrine.orm.entity_manager');
    }

    /**
     * @dataProvider entityDataProvider
     */
    public function testClean(string $input, string $expected): void
    {
        // Given
        $user = new User();
        $user->setCreatedBy('1');
        $user->setLastName($input);

        // When
        $this->em->persist($user);
        $this->em->flush();

        // Then
        $this->tester->assertEquals($expected, $user->getLastName());
    }

    public function entityDataProvider(): array
    {
        return [
            'wrapped with html tag”' => [
                '<html><a href=""></a></html>',
                '<a href=""></a>',
            ],
            'script tag in html”' => [
                '<script>boo</script><a href=""></a>',
                '<a href=""></a>',
            ],
        ];
    }
}
