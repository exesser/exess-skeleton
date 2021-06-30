<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\AwareTrait;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerAwareTrait
{
    protected EntityManagerInterface $em;

    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }
}
