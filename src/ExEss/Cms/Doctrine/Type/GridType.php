<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Cms\Component\Doctrine\Type\AbstractEnumType;

class GridType extends AbstractEnumType
{
    public const LIST = 'list';
    public const EMBEDDED_GUIDANCE = 'embeddedGuidance';

    public static function getValues(): array
    {
        return [
            self::LIST => 'List',
            self::EMBEDDED_GUIDANCE => 'Embedded Guidance',
        ];
    }

    public function getName(): string
    {
        return 'enum_grid_type';
    }
}
