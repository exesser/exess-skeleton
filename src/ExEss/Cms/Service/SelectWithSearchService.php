<?php declare(strict_types=1);

namespace ExEss\Cms\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use ExEss\Cms\Api\V8_Custom\Repository\ListHandler;
use ExEss\Cms\Entity\SelectWithSearch;
use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\ParserService;

class SelectWithSearchService
{
    private ParserService $parserService;
    private ListHandler $listHandler;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        ParserService $parserService,
        ListHandler $listHandler
    ) {
        $this->parserService = $parserService;
        $this->listHandler = $listHandler;
        $this->em = $em;
    }

    /**
     * @param SelectWithSearch|string $selectWithSearch
     */
    public function getSelectOptions(
        $selectWithSearch,
        Model $model,
        int $page = 1,
        ?string $query = null,
        array $keys = [],
        ?string $baseObject = null
    ): array {
        $selectWithSearch = $this->getSelectWithSearchRecord($selectWithSearch, $baseObject);

        $pageSize = $selectWithSearch->getItemsOnPage();

        if ($selectWithSearch->isNeedsQuery() && empty($query)) {
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

        if ($this->em->getMetadataFactory()->hasMetadataFor($selectWithSearch->getBaseObject())) {
            $qb = $this->getQueryBuilder($selectWithSearch, $model, $keys, $query);
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
        }

        $model['query'] = $query;
        $baseFatEntity = $this->listHandler->getList(
            $selectWithSearch->getBaseObject(),
            ['params' => $model->toArray()],
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

    public function getLabelsForValues(
        string $name,
        array $keys,
        ?Model $model = null,
        ?string $baseObject = null
    ): array {
        if (empty($keys)) {
            throw new \InvalidArgumentException("Please provide at least one key to look for!");
        }
        $selectWithSearch = $this->getSelectWithSearchRecord($name, $baseObject);

        if ($this->em->getMetadataFactory()->hasMetadataFor($selectWithSearch->getBaseObject())) {
            $qb = $this->getQueryBuilder($selectWithSearch, $model, $keys);

            return $this->generateRows(
                $qb->getQuery()->execute(),
                $selectWithSearch->getOptionLabel(),
                $selectWithSearch->getOptionKey()
            );
        }

        $model = $model ?? new Model();
        $model['query'] = null;
        $model['keys'] = $keys;

        $baseFatEntity = $this->listHandler->getList(
            $selectWithSearch->getBaseObject(),
            ['params' => $model->toArray()]
        );

        return $this->generateRows(
            $baseFatEntity['list'] ?? [],
            $selectWithSearch->getOptionLabel(),
            $selectWithSearch->getOptionKey()
        );
    }

    private function getQueryBuilder(
        SelectWithSearch $selectWithSearch,
        ?Model $model,
        array $keys,
        ?string $query = null
    ): QueryBuilder {
        $baseObject = $selectWithSearch->getBaseObject();
        $qb = $this->em->getRepository($baseObject)->createQueryBuilder('sws');

        if (!empty($query)) {
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
                $qb->setParameter('query', $query);
            } else {
                $qb->andWhere(
                    " CONCAT('" . $this->generateWhereClauseFromString($filterString, 'sws') . "') LIKE :query"
                );
                $qb->setParameter('query', "%$query%");
            }
        }

        if (!empty($fixedFilters = $selectWithSearch->getFilters())) {
            if ($model instanceof Model) {
                $fixedFilters = $this->parserService->parseListValue($model, $fixedFilters);
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

        if (!empty($keys)) {
            $qb->andWhere("sws.id IN (:keys)");
            $qb->setParameter('keys', $keys);
        }

        return $qb;
    }

    /**
     * @param SelectWithSearch|string $selectWithSearch
     */
    private function getSelectWithSearchRecord($selectWithSearch, ?string $baseObject = null): SelectWithSearch
    {
        if (!$selectWithSearch instanceof SelectWithSearch) {
            $selectWithSearch = $this->em
                ->getRepository(SelectWithSearch::class)
                ->get($selectWithSearch);
        }

        if ($baseObject) {
            $selectWithSearch->setBaseObject($baseObject);
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
                'key' => $this->parserService->parseListValue($item, $key, null),
                'label' => $this->parserService->parseListValue($item, $label, null)
            ];
        }

        return $items;
    }
}
