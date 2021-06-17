<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Repository;

use ExEss\Cms\Doctrine\Type\Locale;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use ExEss\Cms\Repository\TranslationRepository;
use Helper\Testcase\FunctionalTestCase;

class TranslationRepositoryTest extends FunctionalTestCase
{
    private TranslationRepository $translationRepository;

    public function _before(): void
    {
        $this->translationRepository = $this->tester->grabService(TranslationRepository::class);
    }

    public function hasTranslationProvider(): array
    {
        return [
            [
                'tag to translate',
                TranslationDomain::LIST_TITLE,
                Locale::DEFAULT,
                null,
            ],
            [
                'enum to translate',
                TranslationDomain::GUIDANCE_ENUM,
                Locale::DEFAULT,
                'enum_list',
            ],
        ];
    }

    /**
     * @dataProvider hasTranslationProvider
     */
    public function testHasTranslation(
        string $name,
        string $domain,
        string $locale,
        ?string $description
    ): void {
        // Given
        $this->tester->generateTranslation([
            'name' => $name,
            'locale' => $locale,
            'description' => $description,
            'domain' => $domain,
            'translation' => 'some translation',
        ]);

        // When
        $result = $this->translationRepository->exists($name, $domain, $locale, $description);

        // Then
        $this->tester->assertTrue($result);
    }
}
