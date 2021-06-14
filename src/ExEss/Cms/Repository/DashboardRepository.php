<?php declare(strict_types=1);

namespace ExEss\Cms\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ExEss\Cms\Entity\Dashboard;
use ExEss\Cms\Entity\Menu;
use ExEss\Cms\Exception\NotFoundException;

class DashboardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dashboard::class);
    }

    public function get(string $dashboardKey): Dashboard
    {
        $qb = $this->createQueryBuilder('d');

        $dashboard = $qb
            ->andWhere('d.key = :key')
            ->setParameter('key', $dashboardKey)
            ->getQuery()
            ->getSingleResult()
        ;
        if (!$dashboard) {
            throw new NotFoundException("No dashboard $dashboardKey could be found");
        }

        return $dashboard;
    }

    /**
    * @return array|Dashboard[]
    */
    public function getFor(Menu $menu): array
    {
        $qb = $this->createQueryBuilder('d');

        return $qb
            ->innerJoin('d.menus', 'm')
            ->andWhere('m.id = :menu')
            ->orderBy('d.menuSort')
            ->setParameter('menu', $menu->getId())
            ->getQuery()
            ->execute()
        ;
    }
}
