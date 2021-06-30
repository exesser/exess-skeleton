<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class UserStatus extends AbstractEnumType
{
    public const NAME = 'enum_user_status';

    public const ACTIVE = 'Active';
    public const INACTIVE = 'Inactive';

    public static function getValues(): array
    {
        return [
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        ];
    }

    public function getName(): string
    {
        return 'enum_user_status';
    }
}
