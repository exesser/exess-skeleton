<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class Order extends AbstractEnumType
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    public static function getValues(): array
    {
        return [
            self::ASC => 'Ascending',
            self::DESC => 'Descending',
        ];
    }

    public function getName(): string
    {
        return 'enum_order';
    }
}
