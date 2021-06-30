<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\OrderBy;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\Security;
use ExEss\Bundle\CmsBundle\MultiLevelTemplate\TextFunctionHandler;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\ExpressionGroup;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\ExpressionGroupParser;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\ExpressionParserOptions;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\PathResolverOptions;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Translator\QueryTranslator;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class ParserService
{
    private TextFunctionHandler $textFunctionHandler;

    private ExpressionGroupParser $expressionGroupParser;

    private AdapterInterface $cache;

    private Security $security;

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        AdapterInterface $cache,
        Security $security,
        TextFunctionHandler $textFunctionHandler,
        ExpressionGroupParser $expressionGroupParser
    ) {
        $this->textFunctionHandler = $textFunctionHandler;
        $this->expressionGroupParser = $expressionGroupParser;
        $this->cache = $cache;
        $this->security = $security;
        $this->em = $em;
    }

    /**
     * @param ExpressionParserOptions|mixed $baseEntity
     * @return mixed
     * @throws \RuntimeException In case an invalid expression is used or parsing the group failed.
     */
    public function parseListValue(
        $baseEntity,
        ?string $line = null,
        ?string $default = '',
        ?PathResolverOptions $resolverOptions = null
    ) {
        //We get stuff in like:
        //   %contacts(primary_contact_c=true)|first_name% %contacts(primary_contact_c=true)|last_name%
        //   %accounts_paymentdetails|addresses_paymentdetails|address_street% %accounts_paymentdetails
        //   |addresses_paymentdetails|address_housenumber%
        //   %name%
        //   %usageHandler(recordId=id)|low%
        //   %rule_rulecheck_rule_rule(field_name='\%pack_package|name\%')|value%

        // if the baseEntity was wrapped with an options object, use it, if not, wrap it
        if ($baseEntity instanceof ExpressionParserOptions) {
            $parseOptions = $baseEntity;
        } else {
            $parseOptions = new ExpressionParserOptions($baseEntity);
        }

        if (empty($line) || $parseOptions->getBaseEntity() === false) {
            return $default;
        }

        $expressions = new ExpressionGroup($line);
        $hasFunctions = $this->textFunctionHandler->hasFunctions($line);

        // init the results
        $result = (string) $expressions;
        $entity = null;

        if (!\count($expressions->getExpressions())) {
            // Return early if we have nothing to do.
            // We will however still see if there are functions to be handled
            return $this->textFunctionHandler->resolveFunctions($result);
        }

        // parse each expression
        $this->expressionGroupParser->parse($expressions, $parseOptions, $resolverOptions);

        // now process all replacements we have
        foreach ($expressions->getExpressions() as $expression) {
            if ($expression->hasReplacement()) {
                if (\is_array($expression->getReplacement())) {
                    if (\is_array($result)) {
                        foreach ($expression->getReplacement() as $key => $replacement) {
                            $result[$key] = $this->putReplacementIn(
                                $result[$key],
                                (string) $expression,
                                $replacement,
                                $hasFunctions
                            );
                        }
                    } else {
                        $lines = [];
                        foreach ($expression->getReplacement() as $replacement) {
                            $lines[] = $this->putReplacementIn(
                                $result,
                                (string) $expression,
                                $replacement,
                                $hasFunctions
                            );
                        }
                        $result = $lines;
                    }
                } else {
                    // replace the expression with the replacement
                    $result = $this->putReplacementIn(
                        $result,
                        (string) $expression,
                        $expression->getReplacement(),
                        $hasFunctions
                    );
                }
                // to stay BC, $entity must be set with the last processed expression that had a replacement
                // @todo check if this can be fixed, this is only correct for a singular expression, not a group
                $entity = $expression->gotReplacementFrom();
            } else {
                // replace the expression with the default
                $result = $this->putReplacementIn($result, (string) $expression, $default, $hasFunctions);
            }
        }

        // apply text functions to result(s)
        if (\is_array($result)) {
            if ($hasFunctions) {
                $result = \array_map(
                    function ($singleResult) {
                        return $this->textFunctionHandler->resolveFunctions($singleResult);
                    },
                    $result
                );
            }
        } elseif ($result !== '' && $result !== null) {
            if ($hasFunctions) {
                $result = $this->textFunctionHandler->resolveFunctions($result);
            }
        } else {
            $result = '';
            if ($entity === null) {
                // on top of that, no entity found, either a name collision or a bad config
                $result = $default;
            }
        }

        // some old stuff where a true or false string result is transformed to a boolean
        if ($result === 'true') {
            $result = true;
        } elseif ($result === 'false') {
            $result = false;
        } elseif ($result === 'null') {
            $result = null;
        }

        return $result;
    }

    /**
     * @param string|array $result
     * @param null|bool|string $replacement
     *
     * @return string|array
     */
    private function putReplacementIn($result, string $expression, $replacement, bool $hasFunctions = false)
    {
        if ($hasFunctions) {
            if ($replacement === true) {
                $replacement = 'true';
            } elseif ($replacement === false) {
                $replacement = 'false';
            } elseif ($replacement === null) {
                $replacement = 'null';
            }
        }

        return \str_replace('%' . $expression . '%', $replacement, $result);
    }

    /**
     * %id% should be the first expression, @see ExpressionGroup::createForCellsAndTopActions() does this.
     * If using outside list context, make sure you prefix the expressions string to parse with this expression!
     *
     * @throws \RuntimeException If the parser didn't build a Query object.
     */
    public function parseListQuery(
        object $metadata,
        ExpressionGroup $expressions,
        PathResolverOptions $resolverOptions,
        ?OrderBy $orderBy = null,
        bool $fromList = false
    ): array {
        if (!$metadata instanceof ClassMetadata) {
            $metadata = $this->em->getClassMetadata(\get_class($metadata));
        }

        if (empty($allBeanIds = $resolverOptions->getAllBeans())) {
            return [];
        }

        // unset all entities so the paths are cacheable
        $resolverOptions->setAllBeans([]);

        $qb = $this->em->getRepository($metadata->getName())->createQueryBuilder(QueryTranslator::ROOT_ALIAS);
        $resolverOptions->setQueryBuilder($qb);

        // for a given expression group, the map and base query will always be the same
        $item = $this->cache->getItem(
            ($this->security->getCurrentUserId() ?? 'anonymous')
            . '.' . \sha1(((string) $expressions) . $metadata->getName())
        );

        if ($item->isHit()) {
            $dqlParts = $item->get();
            foreach ($dqlParts as $dqlPartName => $dqlPart) {
                if (!empty($dqlPart)) {
                    $qb->add($dqlPartName, $dqlPart);
                }
            }
        } else {
            // parse each expression
            $this->expressionGroupParser->parse(
                $expressions,
                (new ExpressionParserOptions($metadata))->setQueryFormat(),
                $resolverOptions
            );

            // store in cache
            $this->cache->save($item->set($qb->getDQLParts()));
        }

        // add the list's sorting option
        if (!empty($orderBy)) {
            $qb->orderBy($orderBy);
        }
        $qb->addOrderBy('base.id');

        // limit to the requested base entities
        $qb->andWhere($qb->expr()->in('base.id', \array_column($allBeanIds, 'id')));

        // set back all entity ids as it might be needed afterwards
        $resolverOptions->setAllBeans($allBeanIds);

        return $qb->getQuery()->execute();
    }
}
