<?php
namespace ExEss\Cms\Parser\Resolver\Piece;

class NamespacePiece implements PieceInterFace
{
    private string $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
