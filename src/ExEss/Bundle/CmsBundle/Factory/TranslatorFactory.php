<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Factory;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\Doctrine\Type\Locale;
use ExEss\Bundle\CmsBundle\Doctrine\Type\TranslationDomain;
use ExEss\Bundle\CmsBundle\Entity\Translation;
use Psr\Cache\CacheItemPoolInterface;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\Security;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Translator\DatabaseLoader;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Translator\LoggedTranslator;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslatorFactory
{
    private CacheItemPoolInterface $translatorCache;

    private Security $security;

    private DatabaseLoader $databaseLoader;

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        CacheItemPoolInterface $translatorCache,
        DatabaseLoader $databaseLoader,
        Security $security
    ) {
        $this->translatorCache = $translatorCache;
        $this->security = $security;
        $this->databaseLoader = $databaseLoader;
        $this->em = $em;
    }

    public function create(
        bool $writeUntranslatedToDatabase = false,
        bool $cache = true,
        ?string $defaultLocale = null
    ): TranslatorInterface {
        $translator = new LoggedTranslator(
            $this->em,
            $writeUntranslatedToDatabase,
            $this->security->getPreferredLocale() ?? $defaultLocale ?? Locale::DEFAULT,
            $cache? $this->translatorCache: new ArrayAdapter()
        );

        if (!$writeUntranslatedToDatabase && $defaultLocale !== null) {
            $translator->setFallbackLocales([$defaultLocale]);
        }

        $translator->addLoader('database', $this->databaseLoader);

        foreach (\array_keys(TranslationDomain::getValues()) as $domain) {
            foreach (\array_keys(Locale::getValues()) as $locale) {
                $translator->addResource("database", Translation::class, $locale, $domain);
            }
        }

        return $translator;
    }
}
