<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Cms\Component\Doctrine\Type\AbstractEnumType;

class MessageDomain extends AbstractEnumType
{
    public const DEFAULT = self::SIDEBAR;

    public const SIDEBAR = 'sidebar';

    public static function getValues(): array
    {
        return [
            self::SIDEBAR => 'sidebar',
        ];
    }

    public function getName(): string
    {
        return 'enum_message_domain';
    }
}
