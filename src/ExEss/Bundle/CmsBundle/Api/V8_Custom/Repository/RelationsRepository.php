<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use ExEss\Bundle\CmsBundle\Base\Response\Pagination;
use ExEss\Bundle\CmsBundle\Entity\ListDynamic;
use ExEss\Bundle\CmsBundle\Entity\ListSortingOption;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\Response\RelationList;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\Response\RelationRow;
use ExEss\Bundle\CmsBundle\Base\Response\BaseListResponse;
use ExEss\Bundle\CmsBundle\Service\FilterService;

class RelationsRepository extends AbstractRepository
{
    private EntityManagerInterface $em;

    private FilterService $filterService;

    public function __construct(
        EntityManagerInterface $em,
        FilterService $filterService
    ) {
        $this->em = $em;
        $this->filterService = $filterService;
    }

    public function findBy(array $requestData): BaseListResponse
    {
        /** @var ListDynamic $list */
        $list = $requestData['list'];

        [$source, $sourceAssociation] = \explode('::', $list->getBaseObject());

        $sourceMetadata = $this->em->getClassMetadata($source);
        $mapping = $sourceMetadata->getAssociationMapping($sourceAssociation);
        $targetAssociation = ($mapping['isOwningSide'] ? $mapping['inversedBy'] : $mapping['mappedBy']);

        $request = $this->getRequest($requestData);

        $response = new RelationList();

        $response->setPagination(new Pagination(
            $requestData['page'],
            $requestData['limit'],
            \count(new Paginator($request))
        ));

        $reflectionClass = $sourceMetadata->getReflectionClass();
        $property = $reflectionClass->getProperty($sourceAssociation);
        $property->setAccessible(true);

        foreach ($request->getQuery()->getResult() as $sourceEntity) {
            foreach ($property->getValue($sourceEntity) as $targetEntity) {
                $response->addRelation(new RelationRow(
                    $sourceEntity,
                    $sourceAssociation,
                    $targetEntity,
                    $targetAssociation
                ));
            }
        }

        return $response;
    }

    private function addFilters(
        QueryBuilder $qb,
        ArrayCollection $listFilters,
        array $filters,
        string $sourceAssociation,
        string $targetAssociation
    ): void {
        $targetFilters = [];
        $baseFilters = [];

        foreach ($filters as $fieldKey => $values) {
            [$table, $field] = \explode("_I_", $fieldKey);

            if ($table === $sourceAssociation) {
                $targetFilters[$field] = $values;
            } elseif ($table === $targetAssociation) {
                $baseFilters[$field] = $values;
            } else {
                throw new \DomainException(
                    "$fieldKey unknown table for filter, you can use "
                    . "`$sourceAssociation|{field}` or `$targetAssociation|{field}`"
                );
            }
        }

        $this->filterService->addFilterConditions('target', $qb, $listFilters, $targetFilters);
        $this->filterService->addFilterConditions('base', $qb, $listFilters, $baseFilters);
    }

    public function getRequest(array $requestData): QueryBuilder
    {
        /** @var ListDynamic $list */
        $list = $requestData['list'];

        [$source, $sourceAssociation] = \explode('::', $list->getBaseObject());

        $sourceMetadata = $this->em->getClassMetadata($source);
        if (!$sourceMetadata->hasAssociation($sourceAssociation)) {
            throw new \InvalidArgumentException("$source has no association $sourceAssociation");
        }
        $mapping = $sourceMetadata->getAssociationMapping($sourceAssociation);
        $targetAssociation = ($mapping['isOwningSide'] ? $mapping['inversedBy'] : $mapping['mappedBy']);

        $target = $sourceMetadata->getAssociationTargetClass($sourceAssociation);
        $targetMetadata = $this->em->getClassMetadata($target);

        if (
            !$sourceMetadata->isCollectionValuedAssociation($sourceAssociation)
            && !$targetMetadata->isCollectionValuedAssociation($targetAssociation)
        ) {
            throw new \InvalidArgumentException(
                "At least one of the sides must a be a 'many' side of a relation, "
                . "and $source::$sourceAssociation nor $target::$targetAssociation is."
            );
        }

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('base, target')
            ->from($source, 'base')
            ->join("base.$sourceAssociation", 'target')
        ;

        $qb->orderBy($requestData['sortBy'] ?? ListSortingOption::getDefault());

        $this->addFilters(
            $qb,
            $list->getFilterFields(),
            $requestData['filters'] ?? [],
            $sourceAssociation,
            $targetAssociation
        );

        $limit = $requestData['limit'] ?? 20;
        $qb->setFirstResult((($requestData['page'] ?? 1) - 1) * $limit);
        $qb->setMaxResults($limit);

        return $qb;
    }
}
