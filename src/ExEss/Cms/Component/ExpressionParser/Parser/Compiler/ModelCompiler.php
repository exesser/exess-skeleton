<?php declare(strict_types=1);

namespace ExEss\Cms\Component\ExpressionParser\Parser\Compiler;

use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Cms\Component\ExpressionParser\Parser\Resolver\Piece\NamespacePiece;
use ExEss\Cms\Component\ExpressionParser\Parser\Resolver\CompiledPath;

class ModelCompiler
{
    /**
     * @param mixed $object
     */
    public static function shouldHandle($object, string $entityKey): bool
    {
        return $object instanceof Model
            && !empty($object->getNamespace($entityKey));
    }

    public function __invoke(CompiledPath $path, string $entityKey): void
    {
        $path[$entityKey] = new NamespacePiece($entityKey);
    }
}
