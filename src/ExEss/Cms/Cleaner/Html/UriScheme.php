<?php
namespace ExEss\Cms\Cleaner\Html;

class UriScheme extends \HTMLPurifier_URIScheme
{
    /**
     * @var bool
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    public $browsable = true;

    /**
     * @var bool
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    public $may_omit_host = true;

    // @codingStandardsIgnoreLine
    public function doValidate(&$uri, $config, $context): bool
    {
        $uri->userinfo = null;
        $uri->port = null;
        $uri->host = null;
        $uri->query = null;
        $uri->fragment = null;

        return true;
    }
}
