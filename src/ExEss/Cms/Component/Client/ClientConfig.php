<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Client;

class ClientConfig
{
    private string $host;

    private ?string $url = null;

    private ?string $path = null;

    private ?string $query = null;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }
}
