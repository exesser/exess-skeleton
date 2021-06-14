<?php
namespace ExEss\Cms\Parser;

use ExEss\Cms\Exception\ConfigInvalidException;
use ExEss\Cms\Parser\Compiler\AssociationCompiler;
use ExEss\Cms\Parser\Query\Conditions;
use InvalidArgumentException;
use Psr\Cache\CacheItemInterface;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\Parser\Compiler\DateTimeCompiler;
use ExEss\Cms\Parser\Compiler\ExternalLinkCompiler;
use ExEss\Cms\Parser\Compiler\ExternalObjectCompiler;
use ExEss\Cms\Parser\Compiler\ModelCompiler;
use ExEss\Cms\Parser\Compiler\ObjectMethodCompiler;
use ExEss\Cms\Parser\Compiler\ObjectPropertyCompiler;
use ExEss\Cms\Parser\Compiler\RelationListCompiler;
use ExEss\Cms\Parser\Compiler\SecurityCompiler;
use ExEss\Cms\Parser\Query\ConditionsParser;
use ExEss\Cms\Parser\Resolver\Piece\CompiledPathPiece;
use ExEss\Cms\Parser\Resolver\CompiledPath;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class PathCompiler
{
    private ExternalObjectCompiler $externalObjectCompiler;

    private ObjectMethodCompiler $objectMethodCompiler;

    private ObjectPropertyCompiler $objectPropertyCompiler;

    private ExternalLinkCompiler $externalLinkCompiler;

    private AdapterInterface $cache;

    private DateTimeCompiler $dateTimeCompiler;

    private SecurityCompiler $securityCompiler;

    private RelationListCompiler $relationListCompiler;

    private AssociationCompiler $associationCompiler;

    public function __construct(
        AdapterInterface $cache,
        ExternalObjectCompiler $externalObjectCompiler,
        ObjectMethodCompiler $objectMethodCompiler,
        ObjectPropertyCompiler $objectPropertyCompiler,
        ExternalLinkCompiler $externalLinkCompiler,
        DateTimeCompiler $dateTimeCompiler,
        SecurityCompiler $securityCompiler,
        RelationListCompiler $relationListCompiler,
        AssociationCompiler $associationCompiler
    ) {
        $this->externalObjectCompiler = $externalObjectCompiler;
        $this->objectMethodCompiler = $objectMethodCompiler;
        $this->objectPropertyCompiler = $objectPropertyCompiler;
        $this->externalLinkCompiler = $externalLinkCompiler;
        $this->cache = $cache;
        $this->dateTimeCompiler = $dateTimeCompiler;
        $this->securityCompiler = $securityCompiler;
        $this->relationListCompiler = $relationListCompiler;
        $this->associationCompiler = $associationCompiler;
    }

    /**
     * @param Model|object|null $baseEntity
     * @throws InvalidArgumentException In case baseFatEntity is not an object, array or null.
     */
    public function compile(?object $baseEntity, string $fatEntityKey, PathResolverOptions $options): CompiledPath
    {
        $item = null;
        if ($options->isCacheable()) {
            $item = $this->cache->getItem(\sha1(
                (\is_object($baseEntity) ? \get_class($baseEntity) . '|' : '') . "$fatEntityKey - compiled"
            ));
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $path = new CompiledPath();

        if (true === ModelCompiler::shouldHandle($baseEntity, $fatEntityKey)) {
            (new ModelCompiler())($path, $fatEntityKey);
            return $this->storeAndReturn($path, $item);
        }

        // attempt to compile paths that have an irrelevant baseFatEntity or none at all
        // and can't be used in a pipe concatenated path (nor are they cacheable!)
        if ($this->dateTimeCompiler::shouldHandle($fatEntityKey)) {
            /** @see DateTimeCompiler::__invoke() */
            ($this->dateTimeCompiler)($path, $fatEntityKey);
            return $this->storeAndReturn($path, $item);
        }

        if ($this->securityCompiler::shouldHandle($fatEntityKey)) {
            /** @see SecurityCompiler::__invoke() */
            ($this->securityCompiler)($path, $fatEntityKey);
            // signal the caller that this can't be cached, the compiled path itself is still to be cached
            $options->setCacheable(false);
            return $this->storeAndReturn($path, $item);
        }

        // if we had no baseFatEntity, we're done here
        if ($baseEntity === null) {
            return $this->storeAndReturn($path, $item);
        }

        // special cases that can't be used in a pipe concatenated path
        $linkedFatEntity = $baseEntity;
        $conditions = ConditionsParser::parse($fatEntityKey);
        if ($this->externalObjectCompiler::shouldHandle($fatEntityKey)) {
            /** @see ExternalObjectCompiler::__invoke() */
            ($this->externalObjectCompiler)($path, $fatEntityKey, $baseEntity, $conditions, $options);
        } elseif ($this->externalLinkCompiler::shouldHandle($baseEntity, $fatEntityKey, $options)) {
            /** @see ExternalLinkCompiler::__invoke() */
            ($this->externalLinkCompiler)($path, $fatEntityKey, $baseEntity, $options);
        } else {
            // split on all pipes not enclosed by braces
            $pieces = \preg_split(Model::REGEX_PIPES_OUTSIDE_BRACES, $fatEntityKey);
            $currentFatEntityKey = '';
            while ($linkedFatEntity !== null && ($piece = \array_shift($pieces))) {
                $currentFatEntityKey = (!empty($currentFatEntityKey) ? $currentFatEntityKey . '|' : '') . $piece;
                $conditions = ConditionsParser::parse($piece);
                $relation = $conditions->getRelation();

                if ($this->associationCompiler::shouldHandle($linkedFatEntity, $relation)) {
                    /** @see AssociationCompiler::__invoke() */
                    $linkedFatEntity =
                        ($this->associationCompiler)($path, $currentFatEntityKey, $linkedFatEntity, $conditions);

                    if ($conditions->getOrder() === Conditions::DEFAULT_ORDER) {
                        $conditions->setOrder('dateEntered desc');
                    }

                    if ($conditions->getLimit() !== 1 && \count($pieces)) {
                        $path[\substr($currentFatEntityKey, 0, -2)] = $path[$currentFatEntityKey];
                        unset($path[$currentFatEntityKey]);
                        $path[$currentFatEntityKey] = new CompiledPathPiece(
                            $this->compile($linkedFatEntity, \implode('|', $pieces), $options)
                        );
                        // the remaining path was handled in the CompiledPathPiece
                        break;
                    }
                } elseif ($this->objectMethodCompiler::shouldHandle($linkedFatEntity, $relation)) {
                    /** @see ObjectMethodCompiler::__invoke() */
                    $linkedFatEntity =
                        ($this->objectMethodCompiler)($path, $currentFatEntityKey, $linkedFatEntity, $relation);
                } elseif ($this->objectPropertyCompiler::shouldHandle($linkedFatEntity, $relation)) {
                    /** @see ObjectPropertyCompiler::__invoke() */
                    $linkedFatEntity =
                        ($this->objectPropertyCompiler)($path, $currentFatEntityKey, $linkedFatEntity, $relation);
                } elseif ($this->relationListCompiler::shouldHandle($linkedFatEntity)) {
                    /** @see RelationListCompiler::__invoke() */
                    $linkedFatEntity = ($this->relationListCompiler)($path, $currentFatEntityKey, $linkedFatEntity);
                } else {
                    // this is a dead end, there's no way to handle this
                    throw new ConfigInvalidException(
                        "Found no way to compile $currentFatEntityKey on " . \get_class($baseEntity)
                    );
                }
            }
        }

        if (!\is_object($linkedFatEntity) && !\is_array($linkedFatEntity) && $linkedFatEntity !== null) {
            throw new InvalidArgumentException(
                "The path $fatEntityKey should have lead to an object, array of objects or null"
            );
        }

        return $this->storeAndReturn($path, $item);
    }

    private function storeAndReturn(CompiledPath $path, ?CacheItemInterface $item = null): CompiledPath
    {
        if ($item !== null) {
            $item->set($path);
            $this->cache->save($item);
        }

        return $path;
    }
}
