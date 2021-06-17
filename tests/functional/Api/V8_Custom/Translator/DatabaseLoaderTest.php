<?php declare(strict_types=1);

namespace Test\Functional\Api\V8_Custom\Translator;

use ExEss\Cms\Api\V8_Custom\Translator\DatabaseLoader;
use ExEss\Cms\Doctrine\Type\Locale;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use ExEss\Cms\Entity\Translation;
use Helper\Testcase\FunctionalTestCase;

class DatabaseLoaderTest extends FunctionalTestCase
{
    private DatabaseLoader $databaseLoader;

    public function _before(): void
    {
        $this->databaseLoader = $this->tester->grabService(DatabaseLoader::class);
    }

    /**
     * @dataProvider testLoadProvider
     */
    public function testLoad(
        string $name,
        string $locale,
        ?string $translation,
        ?string $description,
        string $domain,
        bool $shouldBeInCatalogue
    ): void {

        $translationRow = \compact(
            'name',
            'locale',
            'translation',
            'description',
            'domain'
        );
        $this->tester->generateTranslation($translationRow);

        // act
        $this->databaseLoader->clearMessages();
        $catalogue = $this->databaseLoader->load(Translation::class, Locale::DEFAULT, TranslationDomain::LIST_TITLE);

        // assert
        $cMessages = $catalogue->all();
        $this->tester->seeInDatabase('trans_translation', $translationRow);
        $domainAndSubdomain = $description ? "{$domain}.{$description}" : $domain;
        if ($shouldBeInCatalogue) {
            $this->tester->assertArrayHasKey($name, $cMessages[$domainAndSubdomain]);
            $this->tester->assertSame($translation, $catalogue->get($name, $domainAndSubdomain));
        } else {
            // if translation is null or empty string should not be in catalogue
            $this->tester->assertFalse(isset($cMessages[$domainAndSubdomain][$name]));
        }
    }

    public function testLoadProvider(): array
    {
        return [
            [
                'name' => 'test1catalogue',
                'locale' => Locale::DEFAULT,
                'translation' => 'test1translation',
                'description' => null,
                'domain' => TranslationDomain::LIST_TITLE,
                'shouldBeInCatalogue' => true,
            ],
            [
                'name' => 'test2catalogue',
                'locale' => Locale::DEFAULT,
                'translation' => 'test2translation',
                'description' => 'desc2',
                'domain' => TranslationDomain::LIST_TITLE,
                'shouldBeInCatalogue' => true,
            ],
            [
                'name' => 'test3catalogue',
                'locale' => Locale::DEFAULT,
                'translation' => null,
                'description' => 'desc3',
                'domain' => TranslationDomain::LIST_TITLE,
                'shouldBeInCatalogue' => false,
            ],
            [
                'name' => 'test4catalogue',
                'locale' => Locale::DEFAULT,
                'translation' => null,
                'description' => null,
                'domain' => TranslationDomain::LIST_TITLE,
                'shouldBeInCatalogue' => false,
            ],
            [
                'name' => 'test5catalogue',
                'locale' => Locale::DEFAULT,
                'translation' => '',
                'description' => null,
                'domain' => TranslationDomain::LIST_TITLE,
                'shouldBeInCatalogue' => true,
            ],
            [
                'name' => 'test6catalogue',
                'locale' => Locale::DEFAULT,
                'translation' => '',
                'description' => 'desc6',
                'domain' => TranslationDomain::LIST_TITLE,
                'shouldBeInCatalogue' => true,
            ],
            [
                'name' => 'test7catalogue',
                'locale' => Locale::DEFAULT,
                'translation' => '   ',
                'description' => 'desc7',
                'domain' => TranslationDomain::LIST_TITLE,
                'shouldBeInCatalogue' => true,
            ],
            [
                'name' => 'test8catalogue',
                'locale' => Locale::DEFAULT,
                'translation' => '   ',
                'description' => null,
                'domain' => TranslationDomain::LIST_TITLE,
                'shouldBeInCatalogue' => true,
            ],

        ];
    }
}
