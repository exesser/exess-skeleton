<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Response\Suggestion;

interface SuggestionInterface extends \JsonSerializable
{
    public function jsonSerialize(): array;
}
