<?php
namespace ExEss\Cms\FLW_Flows\Response\Suggestion;

interface SuggestionInterface extends \JsonSerializable
{
    public function jsonSerialize(): array;
}
