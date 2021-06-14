<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Cms\Component\Doctrine\Type\AbstractEnumType;

class SecurityGroupType extends AbstractEnumType
{
    public const EMPLOYEE = 'EMPLOYEE';
    public const DEALER = 'DEALER';
    public const CUSTOMER = 'CUSTOMER';
    public const DASHBOARD = 'DASHBOARD';
    public const THIRD_PARTY = 'THIRD_PARTY';

    public static function getValues(): array
    {
        return [
            self::DEALER => 'Dealer',
            self::EMPLOYEE => 'Employee',
            self::DASHBOARD => 'Dashboard',
            self::CUSTOMER => 'Customer',
            self::THIRD_PARTY => 'Third Party',
        ];
    }

    public function getName(): string
    {
        return 'enum_security_group_type';
    }
}
