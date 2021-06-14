<?php
namespace ExEss\Cms\Parser\Resolver\Piece;

class MethodPiece implements PieceInterFace
{
    private string $method;

    public function __construct(string $method)
    {
        $this->method = $method;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
