<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Response\Suggestion;

class ValueSuggestion implements SuggestionInterface
{
    private string $name;

    /**
     * @var mixed
     */
    private $value;

    private bool $disabled = false;

    /**
     * @param mixed $value
     */
    public function __construct($value, string $name, bool $disabled = false)
    {
        $this->value = $value;
        $this->name = $name;
        $this->disabled = $disabled;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'name'  => $this->name,
            'value' => $this->value,
            'disabled' => $this->disabled,
        ];
    }
}
