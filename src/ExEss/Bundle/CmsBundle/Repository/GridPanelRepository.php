<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ExEss\Bundle\CmsBundle\Entity\GridPanel;
use ExEss\Bundle\CmsBundle\Exception\NotFoundException;

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
