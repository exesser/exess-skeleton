<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Compiler;

use ExEss\Cms\Api\V8_Custom\Repository\Response\RelationRow;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\CompiledPath;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\PropertyPiece;

class RelationListCompiler
{
    /**
     * @param mixed $baseEntity
     */
    public static function shouldHandle($baseEntity): bool
    {
        return $baseEntity instanceof RelationRow;
    }

    public function __invoke(
        CompiledPath $path,
        string $alias,
        RelationRow $relationRow
    ): object {
        if ($relationRow->getSourceAssociation() === $alias) {
            $baseEntity = $relationRow->getTarget();
            $relation = 'target';
        } elseif ($relationRow->getTargetAssociation() === $alias) {
            $baseEntity = $relationRow->getSource();
            $relation = 'source';
        } else {
            throw new \DomainException(
                \sprintf(
                    'Relation %s not found. Available relations are %s and %s',
                    $alias,
                    $relationRow->getSourceAssociation(),
                    $relationRow->getTargetAssociation()
                )
            );
        }

        $piece = new PropertyPiece($relation);
        $path[$alias] = $piece;

        return $baseEntity;
    }
}
