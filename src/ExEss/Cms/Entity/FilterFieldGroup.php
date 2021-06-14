<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Component\Doctrine\Mapping\Annotation\Auditable;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="fltrs_fieldsgroup", indexes={
 *     @ORM\Index(name="fk_users_id_05b3773d", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_11761dc2", columns={"created_by"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="filterFieldGroups",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_filter_field_group",
 *              joinColumns=@ORM\JoinColumn(name="filter_field_group_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class FilterFieldGroup extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="sort_c", type="string", length=5, nullable=true, options={"default"="10"})
     */
    private ?string $sort = '10';

    /**
     * @ORM\ManyToMany(targetEntity="FilterField", inversedBy="groups")
     * @ORM\OrderBy({"sort" = "asc"})
     * @ORM\JoinTable(name="fltrs_fieldsgroup_fltrs_fields_1_c")
     * @Auditable
     * @var Collection|FilterField[]
     */
    private Collection $fields;

    /**
     * @ORM\ManyToMany(targetEntity="Filter", inversedBy="groups")
     * @ORM\JoinTable(name="fltrs_fieldsgroup_fltrs_filters_1_c")
     * @Auditable
     * @var Collection|Filter[]
     */
    private Collection $filters;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->filters = new ArrayCollection();
    }

    public function getSort(): ?string
    {
        return $this->sort;
    }

    public function setSort(?string $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @return Collection|FilterField[]
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    /**
     * @return Collection|Filter[]
     */
    public function getFilters(): Collection
    {
        return $this->filters;
    }
}
