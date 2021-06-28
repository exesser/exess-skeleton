<?php declare(strict_types=1);

namespace ExEss\Cms\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use ExEss\Cms\Doctrine\Type\HttpMethod;
use ExEss\Cms\Entity\SecurityGroupApi;
use ExEss\Cms\Entity\User;

class SecurityGroupApiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SecurityGroupApi::class);
    }

    public function getUserGroupTypes(string $method, string $route): array
    {
        $qb = $this->createQueryBuilder('sga');

        $qb
            ->andWhere('sga.httpMethod = :method')
            ->andWhere('sga.name = :route')
            ->setParameter('method', $method)
            ->setParameter('route', $route)
        ;

        $types = [];
        /** @var SecurityGroupApi $securityGroupApi */
        foreach ($qb->getQuery()->execute() as $securityGroupApi) {
            foreach (\explode(',', $securityGroupApi->getAllowedGroupTypes()) as $type) {
                $types[\trim($type, '^')] = true;
            }
        }

        return \array_keys($types);
    }

    public function hasMatchedSecurityGroups(User $user, string $route, string $method): bool
    {
        $qb = $this->createQueryBuilder('sga');
        $qb
            ->join('sga.securityGroups', 's')
            ->join('s.userGroups', 'ug')
            ->andWhere('sga.name = :route')
            ->andWhere('ug.user = :user')
            ->setParameter('user', $user)
            ->setParameter('route', $route)
        ;

        if ($method !== HttpMethod::OPTIONS) {
            $qb
                ->andWhere('sga.httpMethod = :method')
                ->setParameter('method', $method)
            ;
        }

        $paginator = new Paginator($qb);

        return \count($paginator) > 0;
    }
}
