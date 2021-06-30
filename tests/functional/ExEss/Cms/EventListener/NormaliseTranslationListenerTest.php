<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\EventListener;

use Doctrine\ORM\EntityManager;
use ExEss\Bundle\CmsBundle\Doctrine\Type\Locale;
use ExEss\Bundle\CmsBundle\Doctrine\Type\TranslationDomain;
use ExEss\Bundle\CmsBundle\Entity\Translation;
use ExEss\Bundle\CmsBundle\Entity\User;
use Helper\Testcase\FunctionalTestCase;

/**
 * @see NormaliseTranslationListener
 */
class NormaliseTranslationListenerTest extends FunctionalTestCase
{
    private EntityManager $em;

    public function _before(): void
    {
        $this->em = $this->tester->grabService('doctrine.orm.entity_manager');
    }

    /**
     * @dataProvider invalidCharactersDataProvider
     */
    public function testReplaceOnUpdate(string $input, string $expectedResult): void
    {
        // Given
        $translationId = $this->tester->generateTranslation([]);

        /** @var Translation $translation */
        $translation = $this->em->getRepository(Translation::class)->find($translationId);
        $translation->setTranslation($input);

        // When
        $this->em->persist($translation);
        $this->em->flush();

        // Then
        $this->tester->assertEquals($expectedResult, $translation->getTranslation());
    }

    /**
     * @dataProvider invalidCharactersDataProvider
     */
    public function testReplaceOnInsert(string $input, string $expectedResult): void
    {
        // Given
        $user = new User();
        $user->setCreatedBy('1');
        $translation = new Translation('foo', TranslationDomain::GUIDANCE_ENUM, Locale::EN);
        $translation->setCreatedBy($user);
        $translation->setTranslation($input);

        // When
        $this->em->persist($user);
        $this->em->persist($translation);
        $this->em->flush();

        // Then
        $this->tester->assertEquals($expectedResult, $translation->getTranslation());
    }

    public function invalidCharactersDataProvider(): array
    {
        return [
            'test “' => [
                'before “ after',
                'before " after',
            ],
            'test ”' => [
                'before ” after',
                'before " after',
            ]
        ];
    }
}
