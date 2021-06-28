<?php
namespace ExEss\Cms\Component\Flow\Response\Suggestion;

interface SuggestionInterface extends \JsonSerializable
{
    public function jsonSerialize(): array;
}
