<?php
namespace ExEss\Bundle\CmsBundle\Cleaner\Html;

class XmpFilter extends \HTMLPurifier_Filter
{
    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    public $name = 'XmpFilter';

    // @codingStandardsIgnoreLine
    public function preFilter($html, $config, $context): string
    {
        return \preg_replace("#<(/)?xmp>#i", "<\\1pre>", $html);
    }
}
