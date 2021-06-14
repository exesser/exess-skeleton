<?php
namespace ExEss\Cms\Parser\Resolver\Piece;

class DateTimePiece implements PieceInterFace
{
    private string $format;

    public function __construct(string $format)
    {
        $this->format = $format;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
