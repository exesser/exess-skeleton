<?php declare(strict_types=1);

namespace ExEss\Cms\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Exception\NotFoundException;

class FlowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flow::class);
    }

    public function get(string $flowKey): Flow
    {
        $qb = $this->createQueryBuilder('f');

        $flow = $qb
            ->andWhere('f.key = :key')
            ->setParameter('key', $flowKey)
            ->getQuery()
            ->getSingleResult()
        ;
        if (!$flow) {
            throw new NotFoundException("No list $flowKey could be found");
        }

        return $flow;
    }
}
