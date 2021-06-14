<?php
namespace ExEss\Cms\Parser\Translator;

use ExEss\Cms\Parser\PathResolverOptions;
use ExEss\Cms\Parser\Resolver\Piece\PieceInterFace;

interface PieceTranslatorInterface
{
    /**
     * @param mixed|object $previous
     * @return mixed
     */
    public function translate(
        PieceInterFace $piece,
        string $fatEntityKey,
        ?object $previous,
        PathResolverOptions $options
    );
}
