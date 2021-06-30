<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\DoctrineExtensionsBundle\Mapping\Annotation\Auditable;
use ExEss\Bundle\CmsBundle\Entity\Base\Join;

/**
 * @ORM\Table(name="securitygroups_users", indexes={
 *     @ORM\Index(name="securitygroups_users_idxd", columns={"user_id", "securitygroup_id"}),
 *     @ORM\Index(name="fk_securitygroups_id_c78f626d", columns={"securitygroup_id"}),
 *     @ORM\Index(name="IDX_3B3227D2A76ED395", columns={"user_id"})
 * })
 * @ORM\Entity
 * @Auditable
 */
class SecurityGroupUser extends Join
{
    /**
     * @ORM\Column(name="primary_group", type="boolean", nullable=true)
     */
    protected ?bool $primaryGroup = null;

    /**
     * @ORM\ManyToOne(targetEntity="SecurityGroup", inversedBy="userGroups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="securitygroup_id", referencedColumnName="id")
     * })
     */
    protected SecurityGroup $securityGroup;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userGroups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    protected User $user;

    public function getSecurityGroup(): SecurityGroup
    {
        return $this->securityGroup;
    }

    public function setPrimaryGroup(bool $primary): void
    {
        $this->primaryGroup = $primary;
    }

    public function isPrimaryGroup(): bool
    {
        return (bool) $this->primaryGroup;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
