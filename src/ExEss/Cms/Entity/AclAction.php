<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Component\Doctrine\Mapping\Annotation\Auditable;
use ExEss\Cms\Entity\Base\Entity;

/**
 * @ORM\Table(name="acl_actions", indexes={
 *     @ORM\Index(name="idx_category_name", columns={"category", "name"})
 * })
 * @Auditable
 * @ORM\Entity
 */
class AclAction extends Entity
{
    /**
     * @ORM\Column(name="category", type="string", length=100, nullable=true)
     */
    private ?string $category = null;

    /**
     * @ORM\Column(name="aclaccess", type="integer", nullable=true)
     */
    private ?int $aclAccess = null;

    /**
     * @ORM\ManyToMany(targetEntity="AclRole", mappedBy="actions")
     * @var Collection|AclRole[]
     */
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getAclAccess(): ?int
    {
        return $this->aclAccess;
    }
}
