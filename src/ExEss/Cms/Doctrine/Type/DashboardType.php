<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Cms\Component\Doctrine\Type\AbstractEnumType;

class DashboardType extends AbstractEnumType
{
    public const DEFAULT = 'DEFAULT';
    public const EXTERNAL = 'EXTERNAL';

    public static function getValues(): array
    {
        return [
            self::DEFAULT => 'Default',
            self::EXTERNAL => 'External',
        ];
    }

    public function getName(): string
    {
        return 'enum_dashboard_type';
    }
}
