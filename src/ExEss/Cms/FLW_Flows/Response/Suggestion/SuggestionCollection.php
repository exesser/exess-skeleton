<?php
namespace ExEss\Cms\FLW_Flows\Response\Suggestion;

use ExEss\Cms\Collection\ObjectCollection;

class SuggestionCollection extends ObjectCollection
{
    public function __construct()
    {
        parent::__construct(SuggestionInterface::class);
    }
}
