<?php
namespace ExEss\Cms\Parser\Compiler;

use Doctrine\Common\Collections\Collection;
use ExEss\Cms\Entity\ExternalObjectLink;
use ExEss\Cms\Parser\PathResolverOptions;
use ExEss\Cms\Parser\Resolver\Piece\ListOfFatEntitiesPiece;
use ExEss\Cms\Parser\Resolver\CompiledPath;

class ExternalLinkCompiler
{
    /**
     * @param mixed $baseFatEntity
     */
    public static function shouldHandle($baseFatEntity, string $fatEntityKey, PathResolverOptions $options): bool
    {
        // @todo re-implement
        return \is_object($baseFatEntity)
            && !$baseFatEntity instanceof \AbstractFatEntity
            && !empty($options->getExternalLinks())
            && !empty(self::findMatch($options->getExternalLinks(), $fatEntityKey))
        ;
    }

    /**
     * @param mixed $baseFatEntity
     */
    public function __invoke(
        CompiledPath $path,
        string $fatEntityKey,
        $baseFatEntity,
        PathResolverOptions $options
    ): void {
        $matchingLinks = self::findMatch($options->getExternalLinks(), $fatEntityKey);
        $externalLink = \current($matchingLinks);

        $piece = new ListOfFatEntitiesPiece(
            $externalLink->suite_bean_name,
            "{$externalLink->suite_bean_field}='#PREVIOUS_VALUE#'",
            1,
            ['#PREVIOUS_VALUE#' => 'get' . \ucfirst($fatEntityKey)]
        );
        $path[$fatEntityKey] = $piece;
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
