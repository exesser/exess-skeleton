<?php

namespace ExEss\Cms\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ExEss\Cms\Entity\FlowAction;
use ExEss\Cms\Exception\NotFoundException;

class FlowActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlowAction::class);
    }

    public function get(string $actionGuid): FlowAction
    {
        $qb = $this->createQueryBuilder('fa');

        $action = $qb
            ->andWhere('fa.guid = :guid')
            ->setParameter('guid', $actionGuid)
            ->getQuery()
            ->getSingleResult()
        ;
        if (!$action) {
            throw new NotFoundException("No flow action $actionGuid could be found");
        }

        return $action;
    }
}
