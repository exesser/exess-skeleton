<?php declare(strict_types=1);

namespace ExEss\Cms\Adapter\Request;

interface Request extends \JsonSerializable
{
    public const METHOD_POST = 'POST';
    public const METHOD_GET = 'GET';
    public function getMethod(): string;
    public function getParameters(): ?array;
    public function getUri(): string;
    public function getHeaders(): array;
    public function withUri(string $uri): Request;
}
