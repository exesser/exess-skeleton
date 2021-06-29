<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser;

class ExpressionParserOptions
{
    private const FORMAT_VALUE = 'VALUE';
    private const FORMAT_QUERY = 'QUERY';

    public const CONTEXT_JSON = 'json';

    /**
     * @var mixed
     */
    private $baseEntity;

    private ?string $fieldType = null;

    private bool $replaceEnumValueWithLabel = false;

    private ?string $language = null;

    private string $format = self::FORMAT_VALUE;

    /**
     * @param mixed $baseEntity
     */
    public function __construct($baseEntity)
    {
        $this->baseEntity = $baseEntity;
    }

    /**
     * @return mixed
     */
    public function getBaseEntity()
    {
        return $this->baseEntity;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language = null): self
    {
        $this->language = $language;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->fieldType;
    }

    public function setContext(string $fieldType): self
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    public function isReplaceEnumValueWithLabel(): bool
    {
        return $this->replaceEnumValueWithLabel;
    }

    public function setReplaceEnumValueWithLabel(bool $replaceEnumValueWithLabel): self
    {
        $this->replaceEnumValueWithLabel = $replaceEnumValueWithLabel;

        return $this;
    }

    public function isQueryFormat(): bool
    {
        return $this->format === static::FORMAT_QUERY;
    }

    public function setQueryFormat(): self
    {
        $this->format = static::FORMAT_QUERY;

        return $this;
    }
}
