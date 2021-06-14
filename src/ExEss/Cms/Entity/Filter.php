<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="fltrs_filters", indexes={
 *     @ORM\Index(name="fk_users_id_0a623723", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_6c9c0cd2", columns={"created_by"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="filters",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_filter",
 *              joinColumns=@ORM\JoinColumn(name="filter_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class Filter extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="default_filters_json_c", type="json", nullable=true)
     */
    private ?array $defaultFiltersJson = null;

    /**
     * @ORM\Column(name="filterskey_c", type="string", length=64, nullable=true)
     */
    private ?string $key = null;

    /**
     * @ORM\ManyToMany(targetEntity="FilterFieldGroup", mappedBy="filters")
     * @ORM\OrderBy({"sort" = "asc"})
     * @var Collection|FilterFieldGroup[]
     */
    private Collection $groups;

    /**
     * @ORM\OneToMany(targetEntity="Dashboard", mappedBy="filter")
     * @var Collection|Dashboard[]
     */
    private Collection $dashboards;

    /**
     * @ORM\OneToMany(targetEntity="ListDynamic", mappedBy="filter")
     * @var Collection|ListDynamic[]
     */
    private Collection $lists;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->dashboards = new ArrayCollection();
        $this->lists = new ArrayCollection();
    }

    public function getDefaultFiltersJson(): ?array
    {
        return $this->defaultFiltersJson;
    }

    public function setDefaultFiltersJson(?array $defaultFiltersJson): void
    {
        $this->defaultFiltersJson = $defaultFiltersJson;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return Collection|FilterFieldGroup[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * @return Collection|Dashboard[]
     */
    public function getDashboards(): Collection
    {
        return $this->dashboards;
    }

    /**
     * @return Collection|ListDynamic[]
     */
    public function getLists(): Collection
    {
        return $this->lists;
    }
}
