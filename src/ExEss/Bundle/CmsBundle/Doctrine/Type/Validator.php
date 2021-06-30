<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class Validator extends AbstractEnumType
{
    public const NOT_BLANK = 'NotBlank';
    public const BLANK = 'Blank';
    public const NOT_NULL = 'NotNull';
    public const IS_NULL = 'IsNull';
    public const IS_TRUE = 'IsTrue';
    public const IS_FALSE = 'IsFalse';
    public const TYPE = 'Type';
    public const EMAIL = 'Email';
    public const LENGTH = 'Length';
    public const URL = 'Url';
    public const RANGE = 'Range';
    public const EQUAL_TO = 'EqualTo';
    public const NOT_EQUAL_TO = 'NotEqualTo';
    public const LESS_THAN = 'LessThan';
    public const LESS_THAN_OR_EQUAL = 'LessThanOrEqual';
    public const GREATER_THAN = 'GreaterThan';
    public const GREATER_THAN_OR_EQUAL = 'GreaterThanOrEqual';
    public const CHOICE = 'Choice';
    public const NOT_IN_LIST = 'NotInList';
    public const IBAN = 'Iban';
    public const REGEX = 'Regex';
    public const VAT = 'Vat';
    public const NACE = 'Nace';
    public const EAN = 'Ean';
    public const IS_NOT_GOS = 'IsNotGos';
    public const ALREADY_CONTRACTED = 'AlreadyContracted';
    public const HAS_RESIDENTIAL = 'HasResidential';
    public const HAS_DROP = 'HasDrop';
    public const HAS_PREPAID = 'HasPrepaid';
    public const PHONE_NUMBER = 'PhoneNumber';
    public const FIXED_PHONE_NUMBER = 'FixedPhoneNumber';
    public const MOBILE_PHONE_NUMBER = 'MobilePhoneNumber';
    public const FILE = 'File';
    public const IS_END_OF_MONTH = 'IsEndOfMonth';
    public const DATE = 'Date';
    public const MULTI_EMAIL = 'MultiEmail';

    public static function getValues(): array
    {
        return [
            self::NOT_BLANK => 'NotBlank',
            self::BLANK => 'Blank',
            self::NOT_NULL => 'NotNull',
            self::IS_NULL => 'IsNull',
            self::IS_TRUE => 'IsTrue',
            self::IS_FALSE => 'IsFalse',
            self::TYPE => 'Type',
            self::EMAIL => 'Email',
            self::LENGTH => 'Length',
            self::URL => 'Url',
            self::RANGE => 'Range',
            self::EQUAL_TO => 'EqualTo',
            self::NOT_EQUAL_TO => 'NotEqualTo',
            self::LESS_THAN => 'LessThan',
            self::LESS_THAN_OR_EQUAL => 'LessThanOrEqual',
            self::GREATER_THAN => 'GreaterThan',
            self::GREATER_THAN_OR_EQUAL => 'GreaterThanOrEqual',
            self::CHOICE => 'Choice',
            self::NOT_IN_LIST => 'NotInList',
            self::IBAN => 'Iban',
            self::REGEX => 'Regex',
            self::VAT => 'Vat',
            self::NACE => 'Nace',
            self::EAN => 'Ean',
            self::IS_NOT_GOS => 'IsNotGos',
            self::ALREADY_CONTRACTED => 'AlreadyContracted',
            self::HAS_RESIDENTIAL => 'HasResidential',
            self::HAS_DROP => 'HasDrop',
            self::HAS_PREPAID => 'HasPrepaid',
            self::PHONE_NUMBER => 'PhoneNumber',
            self::FIXED_PHONE_NUMBER => 'Fixed PhoneNumber',
            self::MOBILE_PHONE_NUMBER => 'Mobile PhoneNumber',
            self::FILE => 'File',
            self::IS_END_OF_MONTH => 'IsEndOfMonth',
            self::DATE => 'Date',
            self::MULTI_EMAIL => 'MultiEmail',
        ];
    }

    public function getName(): string
    {
        return 'enum_validator';
    }
}
