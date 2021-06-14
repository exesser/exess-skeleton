<?php

namespace ExEss\Cms\Cleaner;

use HTMLPurifier;
use HTMLPurifier_Config;
use HTMLPurifier_URISchemeRegistry;
use ExEss\Cms\Cleaner\Html\UriFilter;
use ExEss\Cms\Cleaner\Html\UriScheme;
use ExEss\Cms\Cleaner\Html\XmpFilter;
use Symfony\Component\Filesystem\Filesystem;

class HtmlCleaner
{
    protected HTMLPurifier $purifier;

    public function __construct(
        string $siteUrl,
        string $cacheDir
    ) {
        $config = HTMLPurifier_Config::createDefault();

        (new Filesystem())->mkdir($cacheDir);

        $config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('Core.HiddenElements', ['script' => true, 'style' => true, 'title' => true, 'head' => true]);
        $config->set('Cache.SerializerPath', $cacheDir);
        $config->set('URI.Base', $siteUrl);
        $config->set('CSS.Proprietary', true);
        $config->set('HTML.TidyLevel', 'light');
        $config->set('HTML.ForbiddenElements', ['body' => true, 'html' => true]);
        $config->set('AutoFormat.RemoveEmpty', false);
        // for style
        $config->set('Filter.ExtractStyleBlocks.TidyImpl', false); // can't use csstidy, GPL
        $config->set('Output.FlashCompat', true);
        // for iframe and xmp
        $config->set('Filter.Custom', [new XmpFilter()]);
        // for link
        $config->set('HTML.DefinitionID', 'Sugar HTML Def');
        $config->set('HTML.DefinitionRev', 2);
        // IDs are namespaced
        $config->set('Attr.EnableID', true);
        $config->set('Attr.IDPrefix', 'sugar_text_');

        if ($def = $config->maybeGetRawHTMLDefinition()) {
            $def->addElement(
                'link',   // name
                'Flow',  // content set
                'Empty', // allowed children
                'Core', // attribute collection
                [ // attributes
                    'href*' => 'URI',
                    'rel' => 'Enum#stylesheet', // only stylesheets supported here
                    'type' => 'Enum#text/css' // only CSS supported here
                ]
            );
            $iframe = $def->addElement(
                'iframe',   // name
                'Flow',  // content set
                'Optional: #PCDATA | Flow | Block', // allowed children
                'Core', // attribute collection
                [ // attributes
                    'src*' => 'URI',
                    'frameborder' => 'Enum#0,1',
                    'marginwidth' => 'Pixels',
                    'marginheight' => 'Pixels',
                    'scrolling' => 'Enum#|yes,no,auto',
                    'align' => 'Enum#top,middle,bottom,left,right,center',
                    'height' => 'Length',
                    'width' => 'Length',
                ]
            );
            $iframe->excludes = ['iframe'];
        }
        $uri = $config->getDefinition('URI');
        $uri->addFilter(new UriFilter(), $config);
        HTMLPurifier_URISchemeRegistry::instance()->register('cid', new UriScheme());

        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * Clean string from potential XSS problems
     *
     * @param mixed $html
     * @return mixed
     */
    public function cleanHtml($html)
    {
        if (empty($html)) {
            return $html;
        }

        if (!\preg_match('<[^-A-Za-z0-9 `~!@#$%^&*()_=+{}\[\];:\'",./\\?\r\n|\x80-\xFF]>', $html)) {
            /* if it only has "safe" chars, don't bother */
            return $html;
        }

        return $this->purifier->purify($html);
    }
}
