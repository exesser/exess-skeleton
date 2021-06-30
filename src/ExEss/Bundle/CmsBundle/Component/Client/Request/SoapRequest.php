<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Client\Request;

class SoapRequest implements RequestInterface, \JsonSerializable
{
    private string $function;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @param mixed $data
     */
    public function __construct(string $function, $data)
    {
        $this->function = $function;
        $this->data = $data;
    }

    public function getFunction(): string
    {
        return $this->function;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return $this->data;
    }

    public function getPath(): string
    {
        return $this->function;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            "data" => $this->getData(),
        ];
    }
}
