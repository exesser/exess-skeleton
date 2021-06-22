<?php
namespace ExEss\Cms\Parser\Compiler;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use ExEss\Cms\Entity\ExternalObjectLink;
use ExEss\Cms\Parser\PathResolverOptions;
use ExEss\Cms\Parser\Resolver\Piece\ListOfFatEntitiesPiece;
use ExEss\Cms\Parser\Resolver\CompiledPath;

class ExternalLinkCompiler
{
    private static ClassMetadataFactory $classMetadataFactory;

    public function __construct(EntityManagerInterface $em)
    {
        self::$classMetadataFactory = $em->getMetadataFactory();
    }

    /**
     * @param mixed $baseEntity
     */
    public static function shouldHandle($baseEntity, string $fatEntityKey, PathResolverOptions $options): bool
    {
        return \is_object($baseEntity)
            && !self::$classMetadataFactory->hasMetadataFor(\get_class($baseEntity))
            && !empty($options->getExternalLinks())
            && !empty(self::findMatch($options->getExternalLinks(), $fatEntityKey))
        ;
    }

    public function __invoke(
        CompiledPath $path,
        string $key,
        object $baseEntity,
        PathResolverOptions $options
    ): void {
        $externalLink = self::findMatch($options->getExternalLinks(), $key);

        $piece = new ListOfFatEntitiesPiece(
            $externalLink->getEntityName(),
            "{$externalLink->getEntityField()}='#PREVIOUS_VALUE#'",
            1,
            ['#PREVIOUS_VALUE#' => 'get' . \ucfirst($key)]
        );
        $path[$key] = $piece;
    }

    private static function findMatch(?Collection $externalLinks, string $relation): ?ExternalObjectLink
    {
        if ($externalLinks === null) {
            return null;
        }

        $filtered = $externalLinks->filter(
            function (ExternalObjectLink $externalLink) use ($relation) {
                return $externalLink->getName() === $relation;
            }
        );

        return $filtered->count() > 0 ? $filtered->first() : null;
    }
}
