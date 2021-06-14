<?php

namespace ExEss\Cms\Api\V8_Custom\Translator;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Entity\Translation;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class DatabaseLoader implements LoaderInterface
{
    private array $messages = [];

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public function load($resource, string $locale, string $domain = 'messages')
    {
        $catalogue = new MessageCatalogue($locale);
        foreach ($this->getMessages($resource, $locale, $domain) as $domainSubDomain => $domainMessages) {
            $catalogue->add($domainMessages, $domainSubDomain);
        }

        return $catalogue;
    }

    private function getMessages(string $resource, string $locale, string $requestedDomain): array
    {
        if (isset($this->messages[$resource][$locale])) {
            return $this->messages[$resource][$locale][$requestedDomain] ?? [];
        }

        if (!isset($this->messages[$resource])) {
            $this->messages[$resource] = [];
        }
        $messages = [];

        foreach ($this->em->getRepository(Translation::class)->getFor($locale) as $translation) {
            $domain = $translation['domain'];
            $domainSubDomain = !empty($translation['description']) ? $domain.'.'.$translation['description'] : $domain;
            if (!isset($messages[$domain])) {
                $messages[$domain] = [$domainSubDomain => []];
            }
            $messages[$domain][$domainSubDomain][$translation['name']] = $translation['translation'];
        }

        $this->messages[$resource][$locale] = $messages;

        return $messages[$requestedDomain] ?? [];
    }

    public function clearMessages(): void
    {
        $this->messages = [];
    }
}
