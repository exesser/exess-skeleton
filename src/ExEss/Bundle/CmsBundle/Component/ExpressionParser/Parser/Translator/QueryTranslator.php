<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Translator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\PathResolverOptions;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\AssociationPiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\ExternalObjectPiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\MethodPiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\PieceInterFace;

/**
 * Translates a resolved path piece sub select (part)
 */
class QueryTranslator implements PieceTranslatorInterface
{
    public const ROOT_ALIAS = 'base';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException In case no PathResolverOptions given or previous was no AbstractEntity.
     */
    public function translate(
        PieceInterFace $piece,
        string $entityKey,
        ?object $previous,
        PathResolverOptions $options
    ) {
        if ($piece instanceof ExternalObjectPiece) {
            return null;
        }
        if (!$previous instanceof ClassMetadata) {
            throw new \InvalidArgumentException("Expected a ClassMetadata object");
        }

        if ($piece instanceof AssociationPiece) {
            $qb = $options->getQueryBuilder();
            $joins = $qb->getDQLPart('join');

            $classParts = \explode('\\', $previous->getName());
            $className = \array_pop($classParts);
            $alias = \strtolower("{$className}_{$piece->getRelation()}");

            $joined = false;
            /** @var Join $join */
            foreach ($joins[self::ROOT_ALIAS] ?? [] as $join) {
                if ($join->getAlias() === $alias) {
                    $joined = true;
                }
            }

            if (!$joined) {
                $from = $options->getLastAlias() ?? self::ROOT_ALIAS;
                $qb->leftJoin("$from." . $piece->getRelation(), $alias);
                $qb->addSelect($alias);
            }

            $options->setLastAlias($alias);
            $translated = $this->em->getClassMetadata($previous->getAssociationTargetClass($piece->getRelation()));
        } elseif ($piece instanceof MethodPiece) {
            throw new \InvalidArgumentException(
                "QueryTranslator failed for method {$piece->getMethod()} on class " . \get_class($previous)
            );
        } else {
            throw new \InvalidArgumentException('QueryTranslator does not (yet) support a ' . \get_class($piece));
        }

        return $translated;
    }
}
