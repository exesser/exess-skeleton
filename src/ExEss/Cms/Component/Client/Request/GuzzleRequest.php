<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Client\Request;

use GuzzleHttp\Psr7\Request;

class GuzzleRequest extends Request implements RequestInterface, \JsonSerializable
{
    public const CONTENT_TYPE_JSON = 'application/json';
    public const CONTENT_TYPE_XML = 'application/xml';

    /**
     * @inheritDoc
     */
    public function __construct($method, $uri, array $headers = [], $body = null, $version = '1.1')
    {
        parent::__construct(
            $method,
            $uri,
            $headers + ['Accept' => self::CONTENT_TYPE_JSON, 'Content-Type' => self::CONTENT_TYPE_JSON],
            $body,
            $version
        );
    }

    public function getMethod(): string
    {
        return parent::getMethod();
    }

    public function getHeaders(): array
    {
        return parent::getHeaders();
    }

    public function getData(): string
    {
        $data = parent::getBody()->getContents();
        $this->getBody()->rewind();

        return $data;
    }

    public function getPath(): string
    {
        return (string) parent::getUri();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            "method" => $this->getMethod(),
            "headers" => $this->getHeaders(),
            "data" => $this->getData(),
        ];
    }
}
