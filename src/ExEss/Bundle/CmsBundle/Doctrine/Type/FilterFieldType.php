<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class FilterFieldType extends AbstractEnumType
{
    public const BOOL = 'bool';
    public const VARCHAR = 'varchar';
    public const ENUM = 'enum';
    public const RADIO_GROUP = 'radioGroup';
    public const DATE = 'date';
    public const DATETIME = 'datetime';
    public const CHECKBOX_GROUP = 'checkboxGroup';
    public const TOGGLE_GROUP = 'toggleGroup';
    public const SELECT_WITH_SEARCH = 'selectWithSearch';

    public static function getValues(): array
    {
        return [
            self::BOOL => 'Checkbox',
            self::VARCHAR => 'Input',
            self::ENUM => 'Select',
            self::RADIO_GROUP => 'Radio Group',
            self::DATE => 'Date',
            self::DATETIME => 'Date Time',
            self::CHECKBOX_GROUP => 'Checkbox Group',
            self::TOGGLE_GROUP => 'Toggle Group',
            self::SELECT_WITH_SEARCH => 'Select With Search',
        ];
    }

    public function getName(): string
    {
        return 'enum_filter_field_type';
    }
}
