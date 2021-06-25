<?php declare(strict_types=1);

namespace ExEss\Cms\Component\ExpressionParser\Parser\Compiler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use ExEss\Cms\Component\ExpressionParser\Parser\Query\Conditions;
use ExEss\Cms\Component\ExpressionParser\Parser\Resolver\CompiledPath;
use ExEss\Cms\Component\ExpressionParser\Parser\Resolver\Piece\AssociationPiece;

class AssociationCompiler
{
    private static ClassMetadataFactory $classMetadataFactory;

    public function __construct(EntityManagerInterface $em)
    {
        self::$classMetadataFactory = $em->getMetadataFactory();
    }

    /**
     * @param mixed $entity
     */
    public static function shouldHandle($entity, string $relation): bool
    {
        return \is_object($entity)
            && (
                (
                    $entity instanceof ClassMetadata
                    && $entity->hasAssociation($relation)
                ) || (
                    self::$classMetadataFactory->hasMetadataFor(\get_class($entity))
                    && self::$classMetadataFactory->getMetadataFor(\get_class($entity))->hasAssociation($relation)
                )
            )
        ;
    }

    public function __invoke(
        CompiledPath $path,
        string $association,
        object $metadata,
        Conditions $conditions
    ): ClassMetadata {
        if (!$metadata instanceof ClassMetadata) {
            $metadata = self::$classMetadataFactory->getMetadataFor(\get_class($metadata));
        }

        $path[$association] = new AssociationPiece($conditions);

        return self::$classMetadataFactory->getMetadataFor(
            $metadata->getAssociationTargetClass($conditions->getRelation())
        );
    }
}
