<?php declare(strict_types=1);

namespace ExEss\Cms\Component\ExpressionParser\Parser;

use ExEss\Cms\Exception\ConfigInvalidException;
use ExEss\Cms\Component\ExpressionParser\Parser\Compiler\AssociationCompiler;
use ExEss\Cms\Component\ExpressionParser\Parser\Query\Conditions;
use InvalidArgumentException;
use Psr\Cache\CacheItemInterface;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\Component\ExpressionParser\Parser\Compiler\DateTimeCompiler;
use ExEss\Cms\Component\ExpressionParser\Parser\Compiler\ExternalLinkCompiler;
use ExEss\Cms\Component\ExpressionParser\Parser\Compiler\ExternalObjectCompiler;
use ExEss\Cms\Component\ExpressionParser\Parser\Compiler\ModelCompiler;
use ExEss\Cms\Component\ExpressionParser\Parser\Compiler\ObjectMethodCompiler;
use ExEss\Cms\Component\ExpressionParser\Parser\Compiler\ObjectPropertyCompiler;
use ExEss\Cms\Component\ExpressionParser\Parser\Compiler\RelationListCompiler;
use ExEss\Cms\Component\ExpressionParser\Parser\Compiler\SecurityCompiler;
use ExEss\Cms\Component\ExpressionParser\Parser\Query\ConditionsParser;
use ExEss\Cms\Component\ExpressionParser\Parser\Resolver\Piece\CompiledPathPiece;
use ExEss\Cms\Component\ExpressionParser\Parser\Resolver\CompiledPath;
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
     * @throws InvalidArgumentException In case baseEntity is not an object, array or null.
     */
    public function compile(?object $baseEntity, string $entityKey, PathResolverOptions $options): CompiledPath
    {
        $item = null;
        if ($options->isCacheable()) {
            $item = $this->cache->getItem(\sha1(
                (\is_object($baseEntity) ? \get_class($baseEntity) . '|' : '') . "$entityKey - compiled"
            ));
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $path = new CompiledPath();

        if (true === ModelCompiler::shouldHandle($baseEntity, $entityKey)) {
            (new ModelCompiler())($path, $entityKey);
            return $this->storeAndReturn($path, $item);
        }

        // attempt to compile paths that have an irrelevant baseEntity or none at all
        // and can't be used in a pipe concatenated path (nor are they cacheable!)
        if ($this->dateTimeCompiler::shouldHandle($entityKey)) {
            /** @see DateTimeCompiler::__invoke() */
            ($this->dateTimeCompiler)($path, $entityKey);
            return $this->storeAndReturn($path, $item);
        }

        if ($this->securityCompiler::shouldHandle($entityKey)) {
            /** @see SecurityCompiler::__invoke() */
            ($this->securityCompiler)($path, $entityKey);
            // signal the caller that this can't be cached, the compiled path itself is still to be cached
            $options->setCacheable(false);
            return $this->storeAndReturn($path, $item);
        }

        // if we had no baseEntity, we're done here
        if ($baseEntity === null) {
            return $this->storeAndReturn($path, $item);
        }

        // special cases that can't be used in a pipe concatenated path
        $linkedEntity = $baseEntity;
        $conditions = ConditionsParser::parse($entityKey);
        if ($this->externalObjectCompiler::shouldHandle($entityKey)) {
            /** @see ExternalObjectCompiler::__invoke() */
            ($this->externalObjectCompiler)($path, $entityKey, $baseEntity, $conditions, $options);
        } elseif ($this->externalLinkCompiler::shouldHandle($baseEntity, $entityKey, $options)) {
            /** @see ExternalLinkCompiler::__invoke() */
            ($this->externalLinkCompiler)($path, $entityKey, $baseEntity, $options);
        } else {
            // split on all pipes not enclosed by braces
            $pieces = \preg_split(Model::REGEX_PIPES_OUTSIDE_BRACES, $entityKey);
            $currentEntityKey = '';
            while ($linkedEntity !== null && ($piece = \array_shift($pieces))) {
                $currentEntityKey = (!empty($currentEntityKey) ? $currentEntityKey . '|' : '') . $piece;
                $conditions = ConditionsParser::parse($piece);
                $relation = $conditions->getRelation();

                if ($this->associationCompiler::shouldHandle($linkedEntity, $relation)) {
                    /** @see AssociationCompiler::__invoke() */
                    $linkedEntity =
                        ($this->associationCompiler)($path, $currentEntityKey, $linkedEntity, $conditions);

                    if ($conditions->getOrder() === Conditions::DEFAULT_ORDER) {
                        $conditions->setOrder('dateEntered desc');
                    }

                    if ($conditions->getLimit() !== 1 && \count($pieces)) {
                        $path[\substr($currentEntityKey, 0, -2)] = $path[$currentEntityKey];
                        unset($path[$currentEntityKey]);
                        $path[$currentEntityKey] = new CompiledPathPiece(
                            $this->compile($linkedEntity, \implode('|', $pieces), $options)
                        );
                        // the remaining path was handled in the CompiledPathPiece
                        break;
                    }
                } elseif ($this->objectMethodCompiler::shouldHandle($linkedEntity, $relation)) {
                    /** @see ObjectMethodCompiler::__invoke() */
                    $linkedEntity =
                        ($this->objectMethodCompiler)($path, $currentEntityKey, $linkedEntity, $relation);
                } elseif ($this->objectPropertyCompiler::shouldHandle($linkedEntity, $relation)) {
                    /** @see ObjectPropertyCompiler::__invoke() */
                    $linkedEntity =
                        ($this->objectPropertyCompiler)($path, $currentEntityKey, $linkedEntity, $relation);
                } elseif ($this->relationListCompiler::shouldHandle($linkedEntity)) {
                    /** @see RelationListCompiler::__invoke() */
                    $linkedEntity = ($this->relationListCompiler)($path, $currentEntityKey, $linkedEntity);
                } else {
                    // this is a dead end, there's no way to handle this
                    throw new ConfigInvalidException(
                        "Found no way to compile $currentEntityKey on " . \get_class($baseEntity)
                    );
                }
            }
        }

        if (!\is_object($linkedEntity) && !\is_array($linkedEntity) && $linkedEntity !== null) {
            throw new InvalidArgumentException(
                "The path $entityKey should have lead to an object, array of objects or null"
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
