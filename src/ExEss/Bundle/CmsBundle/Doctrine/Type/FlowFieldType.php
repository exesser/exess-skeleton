<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class FlowFieldType extends AbstractEnumType
{
    public const FIELD_TYPE_HIDDEN = 'hidden';
    public const FIELD_TYPE_LARGE_TEXT_FIELD = 'LargeTextField';
    public const FIELD_TYPE_INPUT_FIELD_GROUP = 'InputFieldGroup';
    public const FIELD_TYPE_ENUM = 'enum';
    public const FIELD_TYPE_TEXT_FIELD = 'TextField';
    public const FIELD_TYPE_BOOL = 'bool';
    public const FIELD_TYPE_DATE = 'date';
    public const FIELD_TYPE_DATETIME = 'datetime';
    public const FIELD_TYPE_TOGGLE = 'toggle';
    public const FIELD_TYPE_TARIFF_CALCULATION = 'tariffCalculation';
    public const FIELD_TYPE_LABEL_AND_TEXT = 'LabelAndText';
    public const FIELD_TYPE_TEXTAREA = 'textarea';
    public const FIELD_TYPE_UPLOAD = 'upload';
    public const FIELD_TYPE_CUSTOM = 'custom';
    public const FIELD_TYPE_SELECT_WITH_SEARCH = 'selectWithSearch';
    public const FIELD_TYPE_LABEL_AND_ACTION = 'LabelAndAction';
    public const FIELD_TYPE_HASHTAG_TEXT = 'hashtagText';
    public const FIELD_TYPE_WYSIWYG = 'wysiwyg';
    public const FIELD_TYPE_DRAW_PAD = 'drawPad';
    public const FIELD_TYPE_JSON = 'json';
    public const FIELD_TYPE_JSON_EDITOR = 'json-editor';

    public static function getValues(): array
    {
        return [
            self::FIELD_TYPE_HIDDEN => 'hidden',
            self::FIELD_TYPE_LARGE_TEXT_FIELD => 'LargeTextField',
            self::FIELD_TYPE_INPUT_FIELD_GROUP => 'InputFieldGroup',
            self::FIELD_TYPE_ENUM => 'enum',
            self::FIELD_TYPE_TEXT_FIELD => 'TextField',
            self::FIELD_TYPE_BOOL => 'bool',
            self::FIELD_TYPE_DATE => 'date',
            self::FIELD_TYPE_DATETIME => 'datetime',
            self::FIELD_TYPE_TOGGLE => 'toggle',
            self::FIELD_TYPE_TARIFF_CALCULATION => 'tariffCalculation',
            self::FIELD_TYPE_LABEL_AND_TEXT => 'LabelAndText',
            self::FIELD_TYPE_TEXTAREA => 'TextArea',
            self::FIELD_TYPE_UPLOAD => 'upload',
            self::FIELD_TYPE_CUSTOM => 'custom',
            self::FIELD_TYPE_SELECT_WITH_SEARCH => 'Select With Search',
            self::FIELD_TYPE_LABEL_AND_ACTION => 'LabelAndAction',
            self::FIELD_TYPE_HASHTAG_TEXT => 'Hashtag TextField',
            self::FIELD_TYPE_WYSIWYG => 'WYSIWYG',
            self::FIELD_TYPE_DRAW_PAD => 'Draw pad',
            self::FIELD_TYPE_JSON => 'Json',
            self::FIELD_TYPE_JSON_EDITOR => 'Json Editor',
        ];
    }

    public function getName(): string
    {
        return 'enum_flow_field_type';
    }
}
