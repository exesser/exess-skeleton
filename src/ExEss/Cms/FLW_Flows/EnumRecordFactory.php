<?php
namespace ExEss\Cms\FLW_Flows;

use Doctrine\DBAL\Types\Type;
use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use Symfony\Component\Translation\Translator;

class EnumRecordFactory
{
    private Translator $translator;
    private Security $security;

    public function __construct(Translator $translator, Security $security)
    {
        $this->translator = $translator;
        $this->security = $security;
    }

    /**
     * @param mixed $key
     */
    public function create($key, ?string $listName, string $value = ''): EnumRecord
    {
        // For enums with integer as key preserve the type. Other types are converted to string to support backwards
        // compatibility.
        if (!\is_integer($key)) {
            $key = (string) $key;
        }

        if ($key === '') {
            $key = null;
        }
        if (!empty($listName) && $key !== null) {
            $value = $this->translate($listName, $key);
        } else {
            $value = $this->translate($listName, $value);
        }

        return new EnumRecord($key, $value);
    }

    /**
     * Attempts to translate the enum key to a translated label, by using the translations,
     * with a fallback to the suite translations. If not found the key is returned.
     */
    public function translate(?string $listName, string $key): string
    {
        $locale = $this->security->getPreferredLocale();

        if (!empty($listName)) {
            $domain = TranslationDomain::GUIDANCE_ENUM . '.' . $listName;
        } else {
            $domain = TranslationDomain::GUIDANCE_ENUM;
        }
        if ($this->translator->getCatalogue($locale)->has($key, $domain)) {
            return $this->translator->trans($key, [], $domain, $locale);
        }

        if (!empty($listName)) {
            $type = Type::getType($listName);
            if (!$type instanceof AbstractEnumType) {
                throw new \InvalidArgumentException("$listName is not a valid enum type");
            }

            $values = $type::getValues();
            if (\array_key_exists($key, $values)) {
                return $values[$key];
            }
        }

        // if still not found, we surely want to add this key to our translations catalogue
        return $this->translator->trans($key, [], $domain, $locale);
    }
}
