<?php
namespace ExEss\Cms\FLW_Flows\Response;

use ExEss\Cms\Collection\ObjectCollection;

class Suggestions implements \JsonSerializable
{
    private ObjectCollection $fieldSuggestions;

    private array $childFieldSuggestions = [];

    public function __construct()
    {
        $this->fieldSuggestions = new ObjectCollection(Suggestion\SuggestionCollection::class);
    }

    public function addFor(string $field, Suggestion\SuggestionInterface $suggestion): void
    {
        if (!isset($this->fieldSuggestions[$field])) {
            $this->fieldSuggestions[$field] = new Suggestion\SuggestionCollection();
        }

        $this->fieldSuggestions[$field][] = $suggestion;
    }

    public function setFor(string $field, Suggestion\SuggestionCollection $suggestionCollection): void
    {
        $this->fieldSuggestions[$field] = $suggestionCollection;
    }

    public function getFor(string $field): Suggestion\SuggestionCollection
    {
        return $this->fieldSuggestions[$field] ?? new Suggestion\SuggestionCollection();
    }

    public function getAll(): ObjectCollection
    {
        return $this->fieldSuggestions;
    }

    public function addForChildField(
        string $repeatKey,
        string $childModelKey,
        string $childField,
        Suggestion\SuggestionInterface $suggestion
    ): void {
        if (!isset($this->childFieldSuggestions[$repeatKey][$childModelKey][$childField])) {
            $this->childFieldSuggestions[$repeatKey] = [
                $childModelKey => [
                    $childField => new Suggestion\SuggestionCollection(),
                ],
            ];
        }

        $this->childFieldSuggestions[$repeatKey][$childModelKey][$childField][] = $suggestion;
    }

    public function setForChildField(
        string $repeatKey,
        string $childModelKey,
        string $childField,
        Suggestion\SuggestionCollection $suggestionCollection
    ): void {
        $this->childFieldSuggestions[$repeatKey][$childModelKey][$childField] = $suggestionCollection;
    }

    public function jsonSerialize(): array
    {
        return \array_merge($this->fieldSuggestions->getArrayCopy(), $this->childFieldSuggestions);
    }
}
