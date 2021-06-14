<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Cms\Component\Doctrine\Type\AbstractEnumType;

class CellVisibility extends AbstractEnumType
{
    public const DEFAULT = 'DEFAULT';
    public const IN_DWP = 'IN_DWP';
    public const IN_CSV = 'IN_CSV';

    public static function getValues(): array
    {
        return [
            self::DEFAULT => 'Default',
            self::IN_DWP => 'In DWP',
            self::IN_CSV => 'In CSV',
        ];
    }

    public function getName(): string
    {
        return 'enum_cell_visibility';
    }
}
