<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Cms\Component\Doctrine\Type\AbstractEnumType;

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
