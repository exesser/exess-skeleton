<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Compiler;

use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\PropertyPiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\CompiledPath;

class ObjectPropertyCompiler
{
    /**
     * @param object|mixed $baseEntity
     */
    public static function shouldHandle($baseEntity, string $relation): bool
    {
        return \is_object($baseEntity)
            && \property_exists($baseEntity, $relation)
            && (\is_object($baseEntity->$relation) || $baseEntity->$relation === null)
        ;
    }

    /**
     * @param mixed|object $baseEntity
     *
     * @return mixed
     */
    public function __invoke(
        CompiledPath $path,
        string $entityKey,
        $baseEntity,
        string $relation
    ) {
        $piece = new PropertyPiece($relation);
        $path[$entityKey] = $piece;

        return $baseEntity->$relation;
    }
}
