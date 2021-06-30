<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class FlowAction extends AbstractEnumType
{
    public const READONLY = 'readOnly';

    public static function getValues(): array
    {
        return [
            self::READONLY => 'Read Only',
        ];
    }

    public function getName(): string
    {
        return 'enum_flow_action';
    }
}
