<?php declare(strict_types=1);

namespace ExEss\Cms\EventListener;

use Doctrine\ORM\Event\PreFlushEventArgs;
use ExEss\Cms\Entity\Translation;

class NormaliseTranslationListener
{
    private const MAP = [
        '"' => ['“','”']
    ];

    public function preFlush(Translation $entity, PreFlushEventArgs $args): void
    {
        if (empty($translation = $entity->getTranslation())) {
            return;
        }

        foreach (self::MAP as $toChar => $fromChars) {
            foreach ($fromChars as $fromChar) {
                $translation = \str_replace($fromChar, $toChar, $translation);
            }
        }

        $entity->setTranslation($translation);
    }
}
