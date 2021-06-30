<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Translator;

use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\PathResolverOptions;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\PieceInterFace;

interface PieceTranslatorInterface
{
    /**
     * @param mixed|object $previous
     * @return mixed
     */
    public function translate(
        PieceInterFace $piece,
        string $entityKey,
        ?object $previous,
        PathResolverOptions $options
    );
}
