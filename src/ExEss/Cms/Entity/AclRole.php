<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\DoctrineExtensionsBundle\Mapping\Annotation\Auditable;
use ExEss\Cms\Entity\Base\Entity;

/**
 * @ORM\Table(name="acl_roles", indexes={
 * })
 * @ORM\Entity
 * @Auditable
 */
class AclRole extends Entity
{
    public const DEFAULT_ROLE_CODE = 'ROLE_USER';

    /**
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private string $code;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="roles")
     * @ORM\JoinTable(name="acl_roles_users")
     * @Auditable
     * @var Collection|User[]
     */
    private Collection $users;

    /**
     * @ORM\ManyToMany(targetEntity="AclAction", inversedBy="roles")
     * @ORM\JoinTable(name="acl_roles_actions")
     * @Auditable
     * @var Collection|AclAction[]
     */
    private Collection $actions;

    /**
     * @ORM\ManyToMany(targetEntity="SecurityGroup", mappedBy="roles")
     * @var Collection|SecurityGroup[]
     */
    private Collection $groups;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function addUser(User $user): void
    {
        $this->users->add($user);
    }
}
