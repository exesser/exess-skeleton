<?php

namespace ExEss\Bundle\CmsBundle\Servicemix\Request\Filters;

class Filter implements \JsonSerializable
{
    private string $field;

    private string $operator;

    private string $value;

    public static function createFrom(string $field, string $operator, string $value): Filter
    {
        $filter = new self();
        $filter->field = $field;
        $filter->operator = $operator;
        $filter->value = $value;

        return $filter;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'field' => $this->getField(),
            'operator' => $this->getOperator(),
            'value' => \is_array($this->getValue()) ? $this->getValue() : [$this->getValue()]
        ];
    }
}
