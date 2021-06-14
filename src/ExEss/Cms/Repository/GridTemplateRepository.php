<?php

namespace ExEss\Cms\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ExEss\Cms\Entity\GridTemplate;
use ExEss\Cms\Exception\NotFoundException;

class GridTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GridTemplate::class);
    }

    public function get(string $gridTemplateKey): GridTemplate
    {
        $qb = $this->createQueryBuilder('gt');

        $flow = $qb
            ->andWhere('gt.key = :key')
            ->setParameter('key', $gridTemplateKey)
            ->getQuery()
            ->getSingleResult()
        ;
        if (!$flow) {
            throw new NotFoundException("No grid template $gridTemplateKey could be found");
        }

        return $flow;
    }
}
