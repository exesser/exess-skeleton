<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class FlowStepType extends AbstractEnumType
{
    public const DEFAULT = 'DEFAULT';

    public static function getValues(): array
    {
        return [
            self::DEFAULT => 'Default',
        ];
    }

    public function getName(): string
    {
        return 'enum_flow_step_type';
    }
}
