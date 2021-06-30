<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class TranslationDomain extends AbstractEnumType
{
    public const DEFAULT = 'messages';

    /**
     * WARNING:
     * Don't use dots (.) in the domain as we will treat this as domain.subdomain when loading translations
     */
    public const MAIN_MENU = 'main-menu';
    public const SUB_MENU = 'sub-menu';
    public const PLUS_MENU = 'plus-menu';
    public const DASHBOARD_GRID = 'dashboard-grid';
    public const SIDEBAR = 'sidebar';
    public const LIST_TITLE = 'list-title';
    public const LIST_TOPBAR = 'list-topbar';
    public const LIST_COLUMN = 'list-column';
    public const LIST_ROWBAR = 'list-rowbar';
    public const LIST_FILTER = 'list-filter';
    public const FLASH_MESSAGE = 'flash-message';
    public const GUIDANCE_GRID = 'guidance-grid';
    public const GUIDANCE_ENUM = 'guidance-enum';
    public const GUIDANCE_TITLE = 'guidance-title';
    public const GUIDANCE_FIELD = 'guidance-field';
    public const ERRORS = 'errors';
    public const SALESCHANNEL = 'saleschannel';
    public const DISCOUNT_DESCRIPTION = 'discount-description';
    public const DISCOUNT_GENERAL_CONDITIONS = 'discount-general-conditions';
    public const UOM = 'uom';
    public const DURATION = 'duration';
    public const PACKAGE_DESCRIPTION = 'package-description';
    public const PACKAGE_GENERAL_CONDITIONS = 'package-general-conditions';
    public const FAT_ENTITY = 'bean';
    public const QUOTE = 'QUOTE';
    public const FREELETTER = 'FREELETTER';
    public const INVOICE = 'INVOICE';
    public const UPSELL_PACKAGE_DESCRIPTION = 'upsell-package-description';
    public const UPSELL_PACKAGE_GENERAL_CONDITIONS = 'upsell-package-general-conditions';
    public const PACKAGE_PRODUCT_DESCRIPTION = 'package-product-description';
    public const PACKAGE_PRODUCT_GENERAL_CONDITIONS = 'package-product-general-conditions';
    public const PACKAGE_PRODUCT_PDF_TITLE = 'package-product-pdf-title';
    public const PACKAGE_PRODUCT_PDF_SUBTITLE = 'package-product-pdf-subtitle';
    public const PACKAGE_PRODUCT_PDF_CONDITIONS = 'package-properties-pdf-conditions';
    public const PACKAGE_PRODUCT_SUMMARY = 'package-product-summary';
    public const PACKAGE_SPECIAL_RIGHT_OF_WITHDRAWAL = 'package-special-right-of-withdrawal';
    public const PACKAGE_SPECIAL_CONDITIONS = 'package-special-conditions';
    public const PACKAGE_PROPERTIES_DESCRIPTION = 'package-properties-description';
    public const PACKAGE_PROPERTIES_GENERAL_CONDITIONS = 'package-properties-general-conditions';
    public const CONDITIONAL_MESSAGE = 'conditional-message';
    public const HASHTAG = 'hashtag';
    public const TEMPLATE_COMMENT = 'template-comment';
    public const SEPA_BLOCK = 'sepa_block';
    public const BODY_ALL = 'body-all';
    public const BODY_POST = 'body-post';
    public const BODY_EMAIL = 'body-email';
    public const BODY_POST_EMAIL = 'body-post-email';
    public const BODY_SMS = 'body-sms';
    public const BODY_RENDER_ONLY = 'body-render-only';
    public const SUBJECT_ALL = 'subject-all';
    public const SUBJECT_POST = 'subject-post';
    public const SUBJECT_EMAIL = 'subject-email';
    public const SUBJECT_POST_EMAIL = 'subject-post-email';
    public const SUBJECT_SMS = 'subject-sms';
    public const SUBJECT_RENDER_ONLY = 'subject-render-only';
    public const JBILLING_ITEM = 'jbilling-item';
    public const MODULE = 'module';
    public const REGISTRATION = 'registration';

    public static function getValues(): array
    {
        $enabled = [
            self::DEFAULT,
            self::MAIN_MENU,
            self::SUB_MENU,
            self::PLUS_MENU,
            self::DASHBOARD_GRID,
            self::SIDEBAR,
            self::LIST_TITLE,
            self::LIST_TOPBAR,
            self::LIST_COLUMN,
            self::LIST_ROWBAR,
            self::LIST_FILTER,
            self::GUIDANCE_GRID,
            self::GUIDANCE_ENUM,
            self::GUIDANCE_TITLE,
            self::GUIDANCE_FIELD,
            self::FLASH_MESSAGE,
            self::ERRORS,
            self::SALESCHANNEL,
            self::DISCOUNT_DESCRIPTION,
            self::DISCOUNT_GENERAL_CONDITIONS,
            self::UOM,
            self::DURATION,
            self::PACKAGE_DESCRIPTION,
            self::PACKAGE_GENERAL_CONDITIONS,
            self::FAT_ENTITY,
            self::QUOTE,
            self::FREELETTER,
            self::UPSELL_PACKAGE_DESCRIPTION,
            self::UPSELL_PACKAGE_GENERAL_CONDITIONS,
            self::PACKAGE_PRODUCT_DESCRIPTION,
            self::PACKAGE_PRODUCT_GENERAL_CONDITIONS,
            self::PACKAGE_PROPERTIES_DESCRIPTION,
            self::PACKAGE_PROPERTIES_GENERAL_CONDITIONS,
            self::PACKAGE_PRODUCT_PDF_TITLE,
            self::PACKAGE_PRODUCT_PDF_SUBTITLE,
            self::PACKAGE_PRODUCT_PDF_CONDITIONS,
            self::PACKAGE_PRODUCT_SUMMARY,
            self::PACKAGE_SPECIAL_RIGHT_OF_WITHDRAWAL,
            self::PACKAGE_SPECIAL_CONDITIONS,
            self::CONDITIONAL_MESSAGE,
            self::HASHTAG,
            self::BODY_POST_EMAIL,
            self::BODY_POST,
            self::BODY_EMAIL,
            self::BODY_SMS,
            self::BODY_RENDER_ONLY,
            self::BODY_ALL,
            self::SUBJECT_POST_EMAIL,
            self::SUBJECT_POST,
            self::SUBJECT_EMAIL,
            self::SUBJECT_SMS,
            self::SUBJECT_RENDER_ONLY,
            self::SUBJECT_ALL,
            self::JBILLING_ITEM,
            self::TEMPLATE_COMMENT,
            self::INVOICE,
            self::MODULE,
            self::SEPA_BLOCK,
            self::REGISTRATION,
        ];

        return \array_combine($enabled, $enabled);
    }

    public function getName(): string
    {
        return 'enum_translation_domain';
    }
}
