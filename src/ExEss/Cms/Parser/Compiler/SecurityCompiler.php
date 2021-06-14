<?php
namespace ExEss\Cms\Parser\Compiler;

use ExEss\Cms\Parser\Resolver\CompiledPath;
use ExEss\Cms\Parser\Resolver\Piece\SecurityPiece;

class SecurityCompiler
{
    public const USER_ID = 'current_user_id';
    public const PRIMARY_GROUP_ID = 'current_primary_group_id';

    public static function shouldHandle(string $relation): bool
    {
        return $relation === static::USER_ID
            || $relation === static::PRIMARY_GROUP_ID
        ;
    }

    /**
     * @throws \InvalidArgumentException In case of an unknown expression type.
     */
    public function __invoke(
        CompiledPath $path,
        string $relation
    ): void {
        switch ($relation) {
            case static::USER_ID:
                $piece = new SecurityPiece('getCurrentUserId');
                break;
            case static::PRIMARY_GROUP_ID:
                $piece = new SecurityPiece('getPrimaryGroupId');
                break;
            default:
                throw new \InvalidArgumentException("Unknown Security relation $relation");
        }
        $path[$relation] = $piece;
    }
}
