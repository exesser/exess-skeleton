<?php declare(strict_types=1);

namespace ExEss\Cms\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ExEss\Cms\Entity\GridPanel;
use ExEss\Cms\Exception\NotFoundException;

class GridPanelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GridPanel::class);
    }

    public function get(string $panelKey): GridPanel
    {
        $qb = $this->createQueryBuilder('gp');

        $gridPanel = $qb
            ->andWhere('gp.key = :key')
            ->setParameter('key', $panelKey)
            ->getQuery()
            ->getSingleResult()
        ;
        if (!$gridPanel) {
            throw new NotFoundException("No grid panel $panelKey could be found");
        }

        return $gridPanel;
    }
}
