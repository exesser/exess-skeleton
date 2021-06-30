<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Translator;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\Entity\Translation;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Service\ResetInterface;

class LoggedTranslator extends Translator implements ResetInterface
{
    private bool $writeUntranslatedToDatabase;

    private AdapterInterface $cache;

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        bool $writeUntranslatedToDatabase,
        string $locale,
        AdapterInterface $cache
    ) {
        $this->writeUntranslatedToDatabase = $writeUntranslatedToDatabase;
        $this->cache = $cache;
        parent::__construct($locale);
        $this->em = $em;
    }

    /**
     * @inheritDoc
     */
    protected function loadCatalogue($locale)
    {
        $cached = $this->cache->getItem(\md5("translation|catalogue|$locale|cached"));

        if ($cached->isHit()) {
            $this->catalogues[$locale] = $cached->get();
            return;
        }

        parent::loadCatalogue($locale);

        if (isset($this->catalogues[$locale])) {
            $cached->set($this->catalogues[$locale]);
            $this->cache->save($cached);
        }
    }

    /**
     * @inheritdoc
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        if (null === $domain) {
            $domain = 'messages';
        }

        $catalogue = $this->getCatalogue($locale);

        if ($this->writeUntranslatedToDatabase
            && !empty($id)
            && !empty($domain)
            && !\array_key_exists($id, $catalogue->all($domain))
        ) {
            $locale = $locale ?? $this->getLocale();
            $description = null;

            $domainParts = \explode('.', $domain);
            if (\count($domainParts) === 2) {
                [$domain, $description] = $domainParts;
            }

            $repo = $this->em->getRepository(Translation::class);
            if (!$repo->exists($id, $domain, $locale, $description)) {
                $this->em->persist(new Translation($id, $domain, $locale, $description));
                $this->em->flush();
            }
        }

        $translation = \strtr($catalogue->get((string) $id, $domain), $parameters);
        if (empty($translation)) {
            $translation = \strtr($id, $parameters);
        }

        return $translation;
    }

    public function reset(): void
    {
        $this->catalogues = [];
        $this->cache->clear();
        foreach ($this->getLoaders() as $loader) {
            if (\method_exists($loader, 'clearMessages')) {
                $loader->clearMessages();
            }
        }
    }
}
