<?php
namespace ExEss\Cms\SecurityGroups;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Db\DbTrait;
use ExEss\Cms\Doctrine\Type\HttpMethod;

class SecurityGroupsRepository
{
    use DbTrait;

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUserGroupTypes(string $method, string $route): array
    {
        $sql = \sprintf(
            "SELECT allowed_usergroups FROM securitygroups_api
                WHERE route = %s
                AND http_method = %s",
            $this->entityManager->getConnection()->quote($route),
            $this->entityManager->getConnection()->quote($method)
        );

        if ($row = $this->entityManager->getConnection()->fetchAssociative($sql)) {
            $types = [];
            foreach (\explode(',', $row['allowed_usergroups']) as $type) {
                $types[] = \trim($type, '^');
            }
            return $types;
        }

        return [];
    }

    public function hasMatchedSecurityGroups(string $userId, string $route, string $method): bool
    {
        $sql = \sprintf(
            "SELECT COUNT(security_group_security_group_api.security_group_api_id) as total 
            FROM security_group_security_group_api
            INNER JOIN securitygroups_api 
                ON security_group_security_group_api.security_group_api_id = securitygroups_api.id
            WHERE security_group_security_group_api.security_group_id IN
                (SELECT securitygroups.id FROM securitygroups
                INNER JOIN securitygroups_users ON securitygroups.id = securitygroups_users.securitygroup_id
                WHERE securitygroups_users.user_id = %s)
            AND securitygroups_api.route = %s",
            $this->entityManager->getConnection()->quote($userId),
            $this->entityManager->getConnection()->quote($route)
        );

        if ($method !== HttpMethod::OPTIONS) {
            $sql .= \sprintf(
                " AND securitygroups_api.http_method = %s",
                $this->entityManager->getConnection()->quote($method)
            );
        }

        return $this->getAllRecords($this->entityManager, $sql)[0]['total'] > 0;
    }
}
