<?php
namespace ExEss\Cms\Parser\Resolver\Piece;

class PropertyPiece implements PieceInterFace
{
    private string $property;

    public function __construct(string $property)
    {
        $this->property = $property;
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}
