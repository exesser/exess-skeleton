<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Cms\Component\Doctrine\Type\AbstractEnumType;

class ValidatorMutator extends AbstractEnumType
{
    public const DAY = 'day';
    public const MONTH = 'month';
    public const YEAR = 'year';

    public static function getValues(): array
    {
        return [
            self::DAY => 'day',
            self::MONTH => 'month',
            self::YEAR => 'year',
        ];
    }

    public function getName(): string
    {
        return 'enum_validator_mutator';
    }
}
