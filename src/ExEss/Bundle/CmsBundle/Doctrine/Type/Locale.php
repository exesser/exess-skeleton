<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class Locale extends AbstractEnumType
{
    public const DEFAULT = self::EN;

    public const NL = 'nl_BE';
    public const EN = 'en_BE';
    public const FR = 'fr_BE';
    public const DE = 'de_DE';

    public static function getValues(): array
    {
        return [
            self::NL => 'Dutch (Belgium)',
            self::EN => 'English (Belgium)',
            self::FR => 'French (Belgium)',
            self::DE => 'German (Germany)',
        ];
    }

    public function getName(): string
    {
        return 'enum_locale';
    }
}
