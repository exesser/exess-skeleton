<?php
namespace ExEss\Bundle\CmsBundle\Cleaner\Html;

use HTMLPurifier_URIFilter;

/**
 * URI filter for HTMLPurifier
 * Approves only resource URIs that are in the list of trusted domains
 * Until we have comprehensive CSRF protection, we need to sanitize URLs in emails, etc.
 * to avoid CSRF attacks.
 */
class UriFilter extends HTMLPurifier_URIFilter
{
    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    public $name = 'UriFilter';

    // @codingStandardsIgnoreLine
    public function filter(&$uri, $config, $context): bool
    {
        // skip non-resource URIs
        if (!$context->get('EmbeddedURI', true)) {
            return true;
        }

        if (!empty($uri->scheme) && \strtolower($uri->scheme) != 'http' && \strtolower($uri->scheme) != 'https') {
            // do not touch non-HTTP URLs
            return true;
        }

        // relative URLs permitted since email templates use it
        // if(empty($uri->host)) return false;
        // allow URLs with no query
        if (empty($uri->query)) {
            return true;
        }

        // Here we try to block URLs that may be used for nasty XSRF stuff by
        // referring back to Sugar URLs
        // allow URLs that don't start with /? or /index.php?
        if (!empty($uri->path) && $uri->path != '/') {
            $lpath = \strtolower($uri->path);
            if (\substr($lpath, -10) != '/index.php' && $lpath != 'index.php') {
                return true;
            }
        }

        $queryItems = [];
        \parse_str($uri->query, $queryItems);
        // weird query, probably harmless
        if (empty($queryItems)) {
            return true;
        }
        // suspiciously like SugarCRM query, reject
        if (!empty($queryItems['module']) && !empty($queryItems['action'])) {
            return false;
        }
        // looks like non-download entry point - allow only specific entry points
        if (!empty($queryItems['entryPoint']) &&
            !\in_array($queryItems['entryPoint'], ['download', 'image', 'getImage'])
        ) {
            return false;
        }

        return true;
    }
}
