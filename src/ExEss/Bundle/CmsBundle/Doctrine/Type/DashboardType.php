<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

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
