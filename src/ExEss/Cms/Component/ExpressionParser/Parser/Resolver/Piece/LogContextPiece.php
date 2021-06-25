<?php declare(strict_types=1);

namespace ExEss\Cms\Component\ExpressionParser\Parser\Resolver\Piece;

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
