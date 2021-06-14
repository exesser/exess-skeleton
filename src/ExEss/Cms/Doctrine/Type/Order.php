<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Cms\Component\Doctrine\Type\AbstractEnumType;

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
