<?php declare(strict_types=1);

namespace ExEss\Cms\Component\ExpressionParser\Parser\Compiler;

use ExEss\Cms\Component\ExpressionParser\Parser\Resolver\Piece\MethodPiece;
use ExEss\Cms\Component\ExpressionParser\Parser\Resolver\CompiledPath;

class ObjectMethodCompiler
{
    /**
     * @param mixed $baseEntity
     */
    public static function shouldHandle($baseEntity, string $relation): bool
    {
        return \is_object($baseEntity)
            && \method_exists($baseEntity, 'get' . \ucfirst($relation))
        ;
    }

    /**
     * @param mixed|object $baseEntity
     * @return mixed
     */
    public function __invoke(
        CompiledPath $path,
        string $entityKey,
        $baseEntity,
        string $relation
    ) {
        $getter = 'get' . \ucfirst($relation);

        $piece = new MethodPiece($getter);
        $path[$entityKey] = $piece;

        return $baseEntity->$getter();
    }
}
