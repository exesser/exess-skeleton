<?php
namespace ExEss\Cms\Db;

use Doctrine\ORM\EntityManagerInterface;

trait DbTrait
{
    /**
     * Get all the records
     */
    private function getAllRecords(EntityManagerInterface $em, string $sql): array
    {
        return $em->getConnection()->fetchAllAssociative($sql);
    }
}
