<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ExEss\Bundle\CmsBundle\Entity\ListDynamic;
use ExEss\Bundle\CmsBundle\Exception\NotFoundException;

class ListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListDynamic::class);
    }

    public function get(string $listName): ListDynamic
    {
        $qb = $this->createQueryBuilder('l');

        $list = $qb
            ->andWhere('l.name = :name')
            ->setParameter('name', $listName)
            ->getQuery()
            ->getSingleResult()
        ;
        if (!$list) {
            throw new NotFoundException("No list $listName could be found");
        }

        return $list;
    }
}
