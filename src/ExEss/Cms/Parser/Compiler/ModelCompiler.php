<?php
namespace ExEss\Cms\Parser\Compiler;

use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\Parser\Resolver\Piece\NamespacePiece;
use ExEss\Cms\Parser\Resolver\CompiledPath;

class ModelCompiler
{
    /**
     * @param mixed $object
     */
    public static function shouldHandle($object, string $fatEntityKey): bool
    {
        return $object instanceof Model
            && !empty($object->getNamespace($fatEntityKey));
    }

    public function __invoke(CompiledPath $path, string $fatEntityKey): void
    {
        $path[$fatEntityKey] = new NamespacePiece($fatEntityKey);
    }
}
