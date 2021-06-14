<?php

namespace ExEss\Cms\CRUD\Handlers;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use ExEss\Cms\Api\V8_Custom\Repository\AbstractRepository;
use ExEss\Cms\Api\V8_Custom\Search\SearchList;
use ExEss\Cms\Base\Response\BaseListResponse;
use ExEss\Cms\Base\Response\Pagination;
use ExEss\Cms\CRUD\Config\CrudMetadata;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Service\FilterService;

class CrudSelectRelationsRepository extends AbstractRepository
{
    private FilterService $filterService;

    private EntityManager $em;

    public function __construct(
        EntityManager $em,
        FilterService $filterService
    ) {
        $this->filterService = $filterService;
        $this->em = $em;
    }

    public function findBy(array $requestData): BaseListResponse
    {
        $request = $this->getRequest($requestData);

        $pagination = new Pagination(
            $requestData['page'],
            $requestData['limit'],
            \count(new Paginator($request))
        );

        $response = new SearchList();
        $response->setPagination($pagination);
        $response->setObjects($request->getQuery()->getResult());

        return $response;
    }

    public function getRequest(array $requestData): QueryBuilder
    {
        $relation = $requestData[Dwp::RELATION_NAME];
        $parentType = $requestData[Dwp::PARENT_TYPE];
        $parentId = $requestData[Dwp::PARENT_ID];

        $metadata = $this->em->getClassMetadata($parentType);
        $target = $metadata->getAssociationTargetClass($relation);
        $repository = $this->em->getRepository($target);

        $mapping = $metadata->getAssociationMapping($relation);

        $qb = $repository->createQueryBuilder('t');
        $qb
            ->join("t." . ($mapping['isOwningSide'] ? $mapping['inversedBy'] : $mapping['mappedBy']), "p")
            ->andWhere("p.id <> :parent")
            ->setParameter("parent", $parentId)
        ;

        if (!empty($requestData['query'])) {
            $this->filterService->addQuickSearchConditions(
                'target',
                $qb,
                CrudMetadata::getQuickSearchFields($target),
                $requestData['query']
            );
        }

        if (!empty($requestData['keys'])) {
            $qb->andWhere($qb->expr()->in('p.id', $requestData['keys']));
        }

        return $qb;
    }
}
