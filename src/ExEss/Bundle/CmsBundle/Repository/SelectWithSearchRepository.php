<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ExEss\Bundle\CmsBundle\Entity\SelectWithSearch;
use ExEss\Bundle\CmsBundle\Exception\NotFoundException;

class SelectWithSearchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SelectWithSearch::class);
    }

    public function get(string $selectWithSearchName): SelectWithSearch
    {
        $qb = $this->createQueryBuilder('sws');

        $selectWithSearch = $qb
            ->andWhere('sws.name = :name')
            ->setParameter('name', $selectWithSearchName)
            ->getQuery()
            ->getSingleResult()
        ;
        if (!$selectWithSearch) {
            throw new NotFoundException("No select with search $selectWithSearchName could be found");
        }

        return $selectWithSearch;
    }
}
