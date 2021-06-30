<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow;

class EnumRecord implements \JsonSerializable
{
    /**
     * @var mixed|null
     */
    public $key;

    /**
     * @var mixed
     */
    public $value;

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return mixed|null
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
        ];
    }
}
