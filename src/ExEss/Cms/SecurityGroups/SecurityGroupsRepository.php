<?php
namespace ExEss\Cms\SecurityGroups;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Doctrine\Type\HttpMethod;

class SecurityGroupsRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUserGroupTypes(string $method, string $route): array
    {
        $sql = \sprintf(
            "SELECT allowed_usergroups FROM securitygroups_api
                WHERE name = %s
                AND http_method = %s",
            $this->em->getConnection()->quote($route),
            $this->em->getConnection()->quote($method)
        );

        if ($row = $this->em->getConnection()->fetchAssociative($sql)) {
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
            AND securitygroups_api.name = %s",
            $this->em->getConnection()->quote($userId),
            $this->em->getConnection()->quote($route)
        );

        if ($method !== HttpMethod::OPTIONS) {
            $sql .= \sprintf(
                " AND securitygroups_api.http_method = %s",
                $this->em->getConnection()->quote($method)
            );
        }

        return $this->em->getConnection()->fetchAllAssociative($sql)[0]['total'] > 0;
    }
}
