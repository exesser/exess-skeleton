<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class CellType extends AbstractEnumType
{
    public const ACTION = 'list_action_cell';
    public const CHECKBOX = 'list_checkbox_cell';
    public const ICON_LINK = 'list_icon_link_cell';
    public const ICON_TEXT = 'list_icon_text_cell';
    public const LINK_BOLD_TOP_TWO_LINER = 'list_link_bold_top_two_liner_cell';
    public const LINK_PINK_DOWN_TOP_TWO_LINER = 'list_link_pink_down_two_liner_cell';
    public const PLUS = 'list_plus_cell';
    public const SIMPLE_TWO_LINER = 'list_simple_two_liner_cell';
    public const DROPDOWN = 'list_dropdown_cell';

    public static function getValues(): array
    {
        return [
            self::ACTION => 'list-action-cell',
            self::CHECKBOX => 'list-checkbox-cell',
            self::ICON_LINK => 'list-icon-link-cell',
            self::ICON_TEXT => 'list-icon-text-cell',
            self::LINK_BOLD_TOP_TWO_LINER => 'list-link-bold-top-two-liner-cell',
            self::LINK_PINK_DOWN_TOP_TWO_LINER => 'list-link-pink-down-two-liner-cell',
            self::PLUS => 'list-plus-cell',
            self::SIMPLE_TWO_LINER => 'list-simple-two-liner-cell',
            self::DROPDOWN => 'list-dropdown-cell',
        ];
    }

    public function getName(): string
    {
        return 'enum_cell_type';
    }
}
