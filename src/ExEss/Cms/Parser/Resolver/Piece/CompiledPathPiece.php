<?php
namespace ExEss\Cms\Parser\Resolver\Piece;

use ExEss\Cms\Parser\Resolver\CompiledPath;

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
