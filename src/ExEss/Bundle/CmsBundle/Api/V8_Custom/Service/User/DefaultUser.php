<?php
namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\User;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\Entity\User;

class DefaultUser
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getSystemUser(): User
    {
        return $this->entityManager->find(User::class, '1');
    }
}
