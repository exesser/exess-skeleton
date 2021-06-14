<?php declare(strict_types=1);

namespace ExEss\Cms\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ExEss\Cms\Entity\Menu;
use ExEss\Cms\Exception\NotFoundException;

class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function get(string $menuName): Menu
    {
        $qb = $this->createQueryBuilder('m');

        $menu = $qb
            ->andWhere('m.name = :name')
            ->setParameter('name', $menuName)
            ->getQuery()
            ->getSingleResult()
        ;
        if (!$menu) {
            throw new NotFoundException("No menu $menuName could be found");
        }

        return $menu;
    }

    /**
    * @return array|Menu[]
    */
    public function getMenus(): array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb
            ->orderBy('m.displayOrder')
            ->getQuery()
            ->execute()
        ;
    }
}
