<?php declare(strict_types=1);

namespace ExEss\Cms\FESelectWithSearch;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\Mapping\MappingException;
use ExEss\Cms\Api\V8_Custom\Repository\ListHandler;
use ExEss\Cms\Entity\SelectWithSearch;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\ListFunctions\HelperClasses\ListHelperFunctions;

class SelectWithSearchService
{
    private ListHelperFunctions $listHelperFunctions;

    private ListHandler $listHandler;

    private EntityManager $em;

    public function __construct(
        EntityManager $em,
        ListHelperFunctions $listHelperFunctions,
        ListHandler $listHandler
    ) {
        $this->listHelperFunctions = $listHelperFunctions;
        $this->listHandler = $listHandler;
        $this->em = $em;
    }

    public function getSelectOptions(string $name, array $args): array
    {
        $selectWithSearch = $this->getSelectWithSearchRecord($name, $args);

        $page = (int) ($args['page'] ?? 1);
        $pageSize = $selectWithSearch->getItemsOnPage();

        if ($selectWithSearch->isNeedsQuery() && empty($args['query'])) {
            return [
                'rows' => [],
                'pagination' => [
                    'page' => 1,
                    'pages' => 1,
                    'pageSize' => $pageSize,
                    'total' => 0,
                ]
            ];
        }

        try {
            $qb = $this->getQueryBuilder($selectWithSearch, $args);
            $qb->setFirstResult(($page - 1) * $pageSize);
            $qb->setMaxResults($pageSize);
            if (!empty($order = $selectWithSearch->getOrderBy())) {
                $qb->orderBy($order);
            }

            $total = \count(new Paginator($qb));

            return [
                'rows' => $this->generateRows(
                    $qb->getQuery()->execute(),
                    $selectWithSearch->getOptionLabel(),
                    $selectWithSearch->getOptionKey()
                ),
                'pagination' => [
                    'page' => $page,
                    'pages' => (int) \ceil($total / $pageSize),
                    'pageSize' => $pageSize,
                    'total' => $total,
                ]
            ];
        } catch (MappingException $e) {
            $args['fullModel']['query'] = $args['query'] ?? null;
            $baseFatEntity = $this->listHandler->getList(
                $selectWithSearch->getBaseObject(),
                ['params' => $args['fullModel']],
                $page,
                $pageSize
            );

            return [
                'rows' => $this->generateRows(
                    $baseFatEntity['list'],
                    $selectWithSearch->getOptionLabel(),
                    $selectWithSearch->getOptionKey()
                ),
                'pagination' => [
                    'page' => $page,
                    'pages' => (int) \ceil($baseFatEntity['total'] / $pageSize),
                    'pageSize' => $pageSize,
                    'total' => (int) $baseFatEntity['total'],
                ],
            ];
        }
    }

    public function getLabelsForValues(string $name, array $keys, array $args = []): array
    {
        if (empty($keys)) {
            throw new \InvalidArgumentException("Please provide at least one key to look for!");
        }
        $selectWithSearch = $this->getSelectWithSearchRecord($name, $args);

        try {
            $args['keys'] = $keys;
            $qb = $this->getQueryBuilder($selectWithSearch, $args);

            return $this->generateRows(
                $qb->getQuery()->execute(),
                $selectWithSearch->getOptionLabel(),
                $selectWithSearch->getOptionKey()
            );
        } catch (MappingException $e) {
            $args['fullModel']['query'] = $args['query'] ?? null;
            $args['fullModel']['keys'] = $keys;

            $baseFatEntity = $this->listHandler->getList(
                $selectWithSearch->getBaseObject(),
                ['params' => $args['fullModel']]
            );

            return $this->generateRows(
                $baseFatEntity['list'] ?? [],
                $selectWithSearch->getOptionLabel(),
                $selectWithSearch->getOptionKey()
            );
        }
    }

    private function getQueryBuilder(SelectWithSearch $selectWithSearch, array $args): QueryBuilder
    {
        $baseObject = $selectWithSearch->getBaseObject();
        $qb = $this->em->getRepository($baseObject)->createQueryBuilder('sws');

        if (!empty($args['query'])) {
            $filterString = $selectWithSearch->getFilterString();
            if (empty($filterString)) {
                throw new \InvalidArgumentException(
                    "Option 'query' passed but the select with search field has no filter string"
                );
            }

            $trimmed = \trim($filterString, '%');
            if ($trimmed === $filterString) {
                // no percentage signs so we only allow an exact match search
                $qb->andWhere("sws.$filterString = :query");
                $qb->setParameter('query', $args['query']);
            } else {
                $qb->andWhere(
                    " CONCAT('" . $this->generateWhereClauseFromString($filterString, 'sws') . "') LIKE :query"
                );
                $qb->setParameter('query', "%$args[query]%");
            }
        }

        if (!empty($fixedFilters = $selectWithSearch->getFilters())) {
            if (isset($args['fullModel'])) {
                $fullModel = \is_array($args['fullModel']) ? new Model($args['fullModel']) : $args['fullModel'];
                if (!$fullModel instanceof Model) {
                    throw new \InvalidArgumentException("fullModel should be an array or a Model");
                }

                $fixedFilters = $this->listHelperFunctions->parseListValue($fullModel, $fixedFilters);
            }

            $parts = \explode(' WHERE ', $fixedFilters);
            if (isset($parts[0], $parts[1]) && \substr($parts[0], 0, 4) === 'JOIN') {
                // we have joins to add
                foreach (\explode(',', \trim(\str_replace('JOIN ', '', $parts[0]))) as $join) {
                    $joinParts = \explode(' ', \trim($join));
                    if (\count($joinParts) !== 2) {
                        throw new \DomainException(
                            "The join part should have contained 2 parts (the column and the alias )"
                        );
                    }
                    $qb->join($joinParts[0], $joinParts[1]);
                }
                $whereClauses = $parts[1];
            } else {
                $whereClauses = $parts[0];
            }

            foreach (\explode(' AND ', $whereClauses) as $filter) {
                if (!empty($filter)) {
                    $qb->andWhere($filter);
                }
            }
        }

        if (!empty($args['keys'])) {
            $qb->andWhere("sws.id IN (:keys)");
            $qb->setParameter('keys', $args['keys']);
        }

        return $qb;
    }

    private function getSelectWithSearchRecord(string $name, array $args): SelectWithSearch
    {
        $selectWithSearch = $this->em
            ->getRepository(SelectWithSearch::class)
            ->get($name);

        foreach ($args['params'] ?? [] as $paramKey => $paramValue) {
            if ($paramKey === 'baseObject') {
                $selectWithSearch->setBaseObject($paramValue);
            } else {
                throw new \DomainException("No idea (yet) how to handle param $paramKey");
            }
        }

        return $selectWithSearch;
    }

    private function generateWhereClauseFromString(string $input, string $alias): string
    {
        return \preg_replace('/\%([^%.]*)\%/', "', IFNULL($alias.\${1}, ''),'", $input);
    }

    private function generateRows(array $listItems, string $label, string $key): array
    {
        $items = [];
        foreach ($listItems as $item) {
            $items[] = [
                'key' => $this->listHelperFunctions->parseListValue($item, $key, null),
                'label' => $this->listHelperFunctions->parseListValue($item, $label, null)
            ];
        }

        return $items;
    }
}
