<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class ValidatorType extends AbstractEnumType
{
    public const BOOL = 'bool';
    public const FLOAT = 'float';
    public const DOUBLE = 'double';
    public const INT = 'int';
    public const LONG = 'long';
    public const NUMERIC = 'numeric';
    public const REAL = 'real';
    public const SCALAR = 'scalar';
    public const ALNUM = 'alnum';
    public const ALPHA = 'alpha';
    public const DIGIT = 'digit';
    public const LOWER = 'lower';
    public const UPPER = 'upper';
    public const CHOICE = 'choice';

    public static function getValues(): array
    {
        return [
            self::BOOL => 'bool',
            self::FLOAT => 'float',
            self::DOUBLE => 'double',
            self::INT => 'int',
            self::LONG => 'long',
            self::NUMERIC => 'numeric',
            self::REAL => 'real',
            self::SCALAR => 'scalar',
            self::ALNUM => 'alnum',
            self::ALPHA => 'alpha',
            self::DIGIT => 'digit',
            self::LOWER => 'lower',
            self::UPPER => 'upper',
            self::CHOICE => 'choice',
        ];
    }

    public function getName(): string
    {
        return 'enum_validator_type';
    }
}
