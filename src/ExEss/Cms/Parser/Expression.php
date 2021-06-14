<?php
namespace ExEss\Cms\Parser;

class Expression
{
    private const GLUE = '|';

    private string $original;

    private bool $parsed = false;

    private bool $hasReplacement = false;

    /**
     * @var mixed
     */
    private $replacement;

    /**
     * @var mixed
     */
    private $gotReplacementFrom;

    private string $path;

    private string $field;

    private ?bool $formatEnums = null;

    /**
     * The string $original contains an expression to be parsed, WITHOUT the enclosing % signs.
     * It represents a single expression to be parsed, no extra text surrounding it.
     */
    public function __construct(string $original)
    {
        $this->original = $original;

        // extract formatting options
        $matches = [];
        \preg_match('/^\{key\}(.*)\{\/key\}$/', $original, $matches);
        if (isset($matches[1])) {
            $original = $matches[1];
            $this->formatEnums = false;
        }

        // transform rel|RELATION expressions to the more common expression RELATION[]|id
        if (\strpos($original, 'rel|') === 0) {
            $original = \str_replace('rel|', '', $original) . '[]|id';
        }
        $parts = \explode(static::GLUE, $original);
        $field = \array_pop($parts);

        $this->field = $field;
        $this->path = \implode(static::GLUE, $parts);
    }

    public function isParsed(): bool
    {
        return $this->parsed;
    }

    public function setParsed(bool $parsed): self
    {
        $this->parsed = $parsed;

        return $this;
    }

    public function hasReplacement(): bool
    {
        return $this->hasReplacement;
    }

    /**
     * @return mixed
     */
    public function getReplacement()
    {
        return $this->replacement;
    }

    /**
     * @return mixed
     */
    public function gotReplacementFrom()
    {
        return $this->gotReplacementFrom;
    }

    /**
     * @param mixed $replacement
     * @param mixed $gotReplacementFrom
     */
    public function setReplacement($replacement, $gotReplacementFrom = null): self
    {
        $this->replacement = $replacement;
        $this->gotReplacementFrom = $gotReplacementFrom;
        $this->hasReplacement = true;

        return $this;
    }

    public function hasPath(): bool
    {
        return !empty($this->path);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function __toString(): string
    {
        return $this->original;
    }

    public function getFormatEnums(): ?bool
    {
        return $this->formatEnums;
    }
}
