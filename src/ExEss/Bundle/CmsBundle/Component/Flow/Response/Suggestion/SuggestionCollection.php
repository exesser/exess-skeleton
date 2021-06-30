<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Response\Suggestion;

use ExEss\Bundle\CmsBundle\Collection\ObjectCollection;

class SuggestionCollection extends ObjectCollection
{
    public function __construct()
    {
        parent::__construct(SuggestionInterface::class);
    }
}
