<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Cms\Component\Doctrine\Type\AbstractEnumType;

class ActionType extends AbstractEnumType
{
    public const CALLBACK = 'CALLBACK';

    public static function getValues(): array
    {
        return [
            self::CALLBACK => 'Callback',
        ];
    }

    public function getName(): string
    {
        return 'enum_action_type';
    }
}
