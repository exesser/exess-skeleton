<?php

namespace ExEss\Cms\ListFunctions\HelperClasses;

class DynamicListSettings
{
    public ?string $title = null;

    public bool $displayFooter = false;

    public \stdClass $actionData;

    public bool $responsive = false;

    public bool $quickSearch = false;

    public function __construct()
    {
        $this->actionData = new \stdClass;
    }

    public function getSlug(): string
    {
        return DynamicListTransliterator::transliterate($this->title, '-');
    }
}
