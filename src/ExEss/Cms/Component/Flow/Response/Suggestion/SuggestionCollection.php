<?php
namespace ExEss\Cms\Component\Flow\Response\Suggestion;

use ExEss\Cms\Collection\ObjectCollection;

class SuggestionCollection extends ObjectCollection
{
    public function __construct()
    {
        parent::__construct(SuggestionInterface::class);
    }
}
