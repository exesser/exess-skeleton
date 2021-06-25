<?php declare(strict_types=1);

namespace ExEss\Cms\Component\ExpressionParser\Parser\Resolver\Piece;

use ExEss\Cms\Component\ExpressionParser\Parser\Resolver\CompiledPath;

class CompiledPathPiece implements PieceInterFace
{
    private CompiledPath $path;

    public function __construct(CompiledPath $path)
    {
        $this->path = $path;
    }

    public function getPath(): CompiledPath
    {
        return $this->path;
    }
}
