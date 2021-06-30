<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Client\Response;

class Response implements ResponseInterface, \JsonSerializable
{
    private int $statusCode;

    private array $headers;

    /**
     * @var string|\stdClass
     */
    private $data;

    /**
     * @param string|\stdClass $data
     */
    public function __construct(int $statusCode, array $headers, $data)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->data = $data;
    }

    /**
     * @return string|\stdClass
     */
    public function getData()
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            "statusCode" => $this->getStatusCode(),
            "headers" => $this->getHeaders(),
            "data" => $this->getData(),
        ];
    }
}
