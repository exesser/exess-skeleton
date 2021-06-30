<?php

namespace ExEss\Bundle\CmsBundle\ListFunctions\HelperClasses;

class DynamicListTopBarButton
{
    public string $label;

    public ?string $CALLBACK;

    public array $action;

    public ?string $icon;

    public bool $enabled;
}
