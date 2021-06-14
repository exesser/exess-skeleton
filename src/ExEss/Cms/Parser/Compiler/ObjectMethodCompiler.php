<?php
namespace ExEss\Cms\Parser\Compiler;

use ExEss\Cms\Parser\Resolver\Piece\MethodPiece;
use ExEss\Cms\Parser\Resolver\CompiledPath;

class ObjectMethodCompiler
{
    /**
     * @param mixed $baseFatEntity
     */
    public static function shouldHandle($baseFatEntity, string $relation): bool
    {
        return \is_object($baseFatEntity)
            && \method_exists($baseFatEntity, 'get' . \ucfirst($relation))
        ;
    }

    /**
     * @param mixed|object $baseFatEntity
     * @return mixed
     */
    public function __invoke(
        CompiledPath $path,
        string $fatEntityKey,
        $baseFatEntity,
        string $relation
    ) {
        $getter = 'get' . \ucfirst($relation);

        $piece = new MethodPiece($getter);
        $path[$fatEntityKey] = $piece;

        return $baseFatEntity->$getter();
    }
}
