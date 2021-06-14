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
use ExEss\Cms\CRUD\Helpers\SecurityService;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\Service\FilterService;

class CrudRecordsRepository extends AbstractRepository
{
    private SecurityService $crudSecurity;

    private EntityManager $em;

    private FilterService $filterService;

    public function __construct(
        EntityManager $em,
        FilterService $filterService,
        SecurityService $crudSecurity
    ) {
        $this->crudSecurity = $crudSecurity;
        $this->em = $em;
        $this->filterService = $filterService;
    }

    public function findBy(array $requestData): BaseListResponse
    {
        $this->crudSecurity->checkIfRecordTypeAllowed($requestData['recordType']);

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

    public function findOneBy(array $requestData): object
    {
        if (!isset($requestData['actionData']['parentType'], $requestData['recordId'])) {
            throw new \InvalidArgumentException('Invalid arguments for ' . __METHOD__);
        }

        return $this->em->getRepository($requestData['actionData']['parentType'])->find($requestData['recordId']);
    }

    public function getRequest(array $requestData): QueryBuilder
    {
        $repository = $this->em->getRepository($requestData['recordType']);
        $qb = $repository->createQueryBuilder('r');

        $qb->setFirstResult(($requestData['page'] - 1) * $requestData['limit']);
        $qb->setMaxResults($requestData['limit']);

        foreach (CrudMetadata::getOrder($requestData['recordType']) as $order) {
            $qb->addOrderBy("r.$order");
        }

        if (!empty($requestData['quickSearch'])) {
            $this->filterService->addQuickSearchConditions(
                'r',
                $qb,
                CrudMetadata::getQuickSearchFields($requestData['recordType']),
                $requestData['quickSearch']
            );
        } else {
            /** @var ListDynamic $list */
            $list = $requestData['list'];
            $this->filterService->addFilterConditions(
                'r',
                $qb,
                $list->getFilterFields(),
                $requestData['filters'] ?? []
            );
        }

        return $qb;
    }
}
