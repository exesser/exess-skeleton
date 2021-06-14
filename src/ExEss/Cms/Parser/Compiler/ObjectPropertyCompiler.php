<?php
namespace ExEss\Cms\Parser\Compiler;

use ExEss\Cms\Parser\Resolver\Piece\PropertyPiece;
use ExEss\Cms\Parser\Resolver\CompiledPath;

class ObjectPropertyCompiler
{
    /**
     * @param object|mixed $baseFatEntity
     */
    public static function shouldHandle($baseFatEntity, string $relation): bool
    {
        return \is_object($baseFatEntity)
            && \property_exists($baseFatEntity, $relation)
            && (\is_object($baseFatEntity->$relation) || $baseFatEntity->$relation === null)
        ;
    }

    /**
     * @param mixed|object $baseFatEntity
     *
     * @return mixed
     */
    public function __invoke(
        CompiledPath $path,
        string $fatEntityKey,
        $baseFatEntity,
        string $relation
    ) {
        $piece = new PropertyPiece($relation);
        $path[$fatEntityKey] = $piece;

        return $baseFatEntity->$relation;
    }
}
