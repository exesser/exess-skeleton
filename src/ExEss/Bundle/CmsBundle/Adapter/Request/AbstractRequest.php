<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Adapter\Request;

abstract class AbstractRequest implements Request
{
    protected array $parameters;

    protected string $uri;

    protected array $headers;

    public function __construct(array $parameters = [], array $headers = [])
    {
        $this->parameters = $parameters;
        $this->headers = $headers;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function withUri(string $uri): Request
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function toArray(): array
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
