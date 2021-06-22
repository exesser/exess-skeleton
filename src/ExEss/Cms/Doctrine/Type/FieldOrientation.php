<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class FieldOrientation extends AbstractEnumType
{
    public const LABEL_LEFT = 'label-left';
    public const LABEL_TOP = 'label-top';
    public const HEADER_TOP = 'header-top';

    public static function getValues(): array
    {
        return [
            self::LABEL_LEFT => 'Label Left',
            self::LABEL_TOP => 'Label Top',
            self::HEADER_TOP => 'Header Top',
        ];
    }

    public function getName(): string
    {
        return 'enum_field_orientation';
    }
}
