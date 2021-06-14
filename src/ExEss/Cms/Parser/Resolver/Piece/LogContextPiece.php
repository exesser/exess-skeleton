<?php
namespace ExEss\Cms\Parser\Resolver\Piece;

class LogContextPiece implements PieceInterFace
{
    private \stdClass $context;

    public function __construct(\stdClass $context)
    {
        $this->context = $context;
    }

    public function getContext(): \stdClass
    {
        return $this->context;
    }
}
