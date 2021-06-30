<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\CRUD\Handlers;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use ExEss\Bundle\CmsBundle\Base\Response\Pagination;
use ExEss\Bundle\CmsBundle\CRUD\Config\CrudMetadata;
use ExEss\Bundle\CmsBundle\Entity\ListDynamic;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\AbstractRepository;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Search\SearchList;
use ExEss\Bundle\CmsBundle\Base\Response\BaseListResponse;
use ExEss\Bundle\CmsBundle\Service\FilterService;

class CrudRecordRelationsRepository extends AbstractRepository
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
        /** @var ListDynamic $list */
        $list = $requestData['list'];

        $request = $this->getRequest($requestData);

        $pagination = new Pagination(
            $requestData['page'],
            $list->getItemsPerPage(),
            \count(new Paginator($request))
        );

        $response = new SearchList();
        $response->setPagination($pagination);
        $response->setObjects($request->getQuery()->getResult());

        return $response;
    }

    public function getRequest(array $requestData): QueryBuilder
    {
        $relation = $requestData['extraActionData']['relationName'];
        $parentType = $requestData['extraActionData']['parentType'];
        $parentId = $requestData['extraActionData']['parentId'];

        $metadata = $this->em->getClassMetadata($parentType);
        $target = $metadata->getAssociationTargetClass($relation);
        $repository = $this->em->getRepository($target);

        $mapping = $metadata->getAssociationMapping($relation);

        $qb = $repository->createQueryBuilder('t');
        $qb
            ->join("t." . ($mapping['isOwningSide'] ? $mapping['inversedBy'] : $mapping['mappedBy']), "p")
            ->andWhere("p.id = :parent")
            ->setParameter("parent", $parentId)
        ;

        if (!empty($requestData['quickSearch'])) {
            $this->filterService->addQuickSearchConditions(
                'target',
                $qb,
                CrudMetadata::getQuickSearchFields($target),
                $requestData['quickSearch']
            );
        }

        return $qb;
    }

    public function findOneBy(array $requestData): object
    {
        if (!isset($requestData['actionData']['recordType'], $requestData['recordId'])) {
            throw new \InvalidArgumentException('Invalid arguments for ' . __METHOD__);
        }

        return $this->em->getRepository($requestData['actionData']['recordType'])->find($requestData['recordId']);
    }
}
