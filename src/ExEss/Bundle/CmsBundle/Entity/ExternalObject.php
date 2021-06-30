<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;

/**
 * @ORM\Table(name="list_external_object", indexes={
 *     @ORM\Index(name="fk_users_id_b65dbf45", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_911f78c5", columns={"created_by"})
 * })
 * @ORM\Entity
 */
class ExternalObject extends Entity
{
    /**
     * @ORM\Column(name="class_handler", type="string", length=255, nullable=true)
     */
    private ?string $classHandler = null;

    /**
     * @ORM\Column(name="module_name_c", type="string", length=255, nullable=true)
     */
    private ?string $moduleName = null;

    /**
     * @ORM\Column(name="list_only", type="boolean", nullable=false, options={"default"="0"})
     */
    private bool $listOnly = false;

    /**
     * @ORM\OneToMany(targetEntity="ExternalObjectLink", mappedBy="externalObject")
     * @var Collection|ExternalObjectLink[]
     */
    private Collection $linkFields;

    /**
     * @ORM\OneToMany(targetEntity="ListDynamic", mappedBy="externalObject")
     * @var Collection|ListDynamic[]
     */
    private Collection $lists;

    public function __construct()
    {
        $this->linkFields = new ArrayCollection();
        $this->lists = new ArrayCollection();
    }

    public function getClassHandler(): ?string
    {
        return $this->classHandler;
    }

    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }

    public function isListOnly(): bool
    {
        return $this->listOnly;
    }

    /**
     * @return Collection|ExternalObjectLink[]
     */
    public function getLinkFields(): Collection
    {
        return $this->linkFields;
    }

    /**
     * @return Collection|ListDynamic[]
     */
    public function getLists(): Collection
    {
        return $this->lists;
    }
}
