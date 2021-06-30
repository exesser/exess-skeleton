<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece;

class ContainerMethodPiece implements PieceInterFace
{
    private string $service;

    private string $method;

    private array $arguments;

    public function __construct(string $service, string $method, array $arguments = [])
    {
        $this->service = $service;
        $this->method = $method;
        $this->arguments = $arguments;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }
}
