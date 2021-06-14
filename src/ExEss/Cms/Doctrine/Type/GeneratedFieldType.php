<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Cms\Component\Doctrine\Type\AbstractEnumType;

class GeneratedFieldType extends AbstractEnumType
{
    public const FIXED = 'fixed';
    public const REPEAT_TRIGGER = 'repeat-trigger';

    public static function getValues(): array
    {
        return [
            self::FIXED => 'fixed',
            self::REPEAT_TRIGGER => 'repeat-trigger',
        ];
    }

    public function getName(): string
    {
        return 'enum_generated_field_type';
    }
}
