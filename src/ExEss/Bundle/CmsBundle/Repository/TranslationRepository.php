<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use ExEss\Bundle\CmsBundle\Entity\Translation;

class TranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Translation::class);
    }

    public function exists(
        string $name,
        string $domain,
        string $locale,
        ?string $description
    ): bool {
        $qb = $this->createQueryBuilder('t');
        $expr = $qb->expr();

        $qb
            ->andWhere('t.name = :name')
            ->andWhere('t.domain = :domain')
            ->andWhere('t.locale = :locale')
            ->setParameter('name', $name)
            ->setParameter('domain', $domain)
            ->setParameter('locale', $locale)
        ;
        if ($description === null) {
            $qb->andWhere($expr->isNull('t.description'));
        } else {
            $qb
                ->andWhere('t.description = :description')
                ->setParameter('description', $description)
            ;
        }

        return \count(new Paginator($qb)) > 0;
    }

    public function getFor(string $locale): array
    {
        $qb = $this->createQueryBuilder('t');
        $expr = $qb->expr();

        $qb
            ->select('t.name, t.description, t.domain, t.translation')
            ->andWhere('t.locale = :locale')
            ->andWhere($expr->isNotNull('t.translation'))
            ->setParameter('locale', $locale)
        ;

        return $qb->getQuery()->getArrayResult();
    }
}
