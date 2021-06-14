<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="securitygroups_api", indexes={
 *     @ORM\Index(name="idx_route", columns={"route"}),
 *     @ORM\Index(name="fk_users_id_9d312a8e", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_059e836c", columns={"created_by"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="apis",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_security_group_api",
 *              joinColumns=@ORM\JoinColumn(name="security_group_api_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class SecurityGroupApi extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="http_method", type="enum_http_method", nullable=true)
     */
    private ?string $httpMethod = null;

    /**
     * @ORM\Column(name="route", type="string", length=255, nullable=false)
     */
    private string $route;

    /**
     * @ORM\Column(name="allowed_usergroups", type="text", length=65535, nullable=true)
     */
    private ?string $allowedGroupTypes = null;
}
