<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Adapter\Response;

use ExEss\Bundle\CmsBundle\Adapter\Response\Response as ResponseInterface;

abstract class AbstractResponse extends \ArrayObject implements ResponseInterface
{
    private int $code;

    private array $body;

    private array $headers;

    public function __construct(int $code, ?array $body, array $headers = [])
    {
        $this->code = $code;
        $this->body = $body;
        $this->headers = $headers;

        parent::__construct($body ?? []);
    }

    public function getStatusCode(): int
    {
        return $this->code;
    }

    public function getBody(): ?array
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
