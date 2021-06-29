<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver;

use ExEss\Cms\Collection\ObjectCollection;

class CompiledPath extends ObjectCollection
{
    public function __construct()
    {
        parent::__construct(Piece\PieceInterFace::class);
    }

    /**
     * Override to avoid fiddling with the keys
     * @inheritdoc
     */
    protected function transformToClassName($index = null)
    {
        return $index;
    }
}
