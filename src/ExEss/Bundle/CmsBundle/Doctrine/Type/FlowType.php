<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class FlowType extends AbstractEnumType
{
    public const STANDARD = 'STANDARD';
    public const DASHBOARD = 'Dashvoard';
    public const DEFAULT = 'DEFAULT';
    public const FORCE_CREATE = 'FORCECREATE';

    public static function getValues(): array
    {
        return [
            self::STANDARD  => 'Standard',
            self::DASHBOARD => 'Dashboard',
            self::DEFAULT => 'Default',
            self::FORCE_CREATE => 'Force create',
        ];
    }

    public function getName(): string
    {
        return 'enum_flow_type';
    }
}
