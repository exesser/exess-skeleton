<?php
namespace ExEss\Cms\Parser\Compiler;

use ExEss\Cms\Parser\Resolver\Piece\DateTimePiece;
use ExEss\Cms\Parser\Resolver\CompiledPath;

class DateTimeCompiler
{
    private const TODAY = 'TODAY';
    private const NOW = 'NOW';

    public static function shouldHandle(string $relation): bool
    {
        return $relation === static::NOW || $relation === static::TODAY;
    }

    /**
     * @throws \InvalidArgumentException In case of an unknown expression type.
     */
    public function __invoke(
        CompiledPath $path,
        string $relation
    ): void {
        switch ($relation) {
            case static::TODAY:
                $piece = new DateTimePiece('Y-m-d');
                break;
            case static::NOW:
                $piece = new DateTimePiece('Y-m-d H:i:s');
                break;
            default:
                throw new \InvalidArgumentException("Unknown DateTime relation $relation");
        }
        $path[$relation] = $piece;
    }
}
