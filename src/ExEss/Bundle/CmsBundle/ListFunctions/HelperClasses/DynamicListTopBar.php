<?php

namespace ExEss\Bundle\CmsBundle\ListFunctions\HelperClasses;

class DynamicListTopBar
{
    public bool $selectAll;

    public bool $canExportToCSV;

    public array $buttons = [];

    public array $filters = [];

    public array $sortingOptions = [];
}
