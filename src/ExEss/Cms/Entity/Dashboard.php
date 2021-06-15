<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Doctrine\Type\DashboardType;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Component\Doctrine\Mapping\Annotation\Auditable;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="dash_dashboard", indexes={
 *     @ORM\Index(name="fk_grid_gridtemplates_id_61577793", columns={"grid_gridtemplates_id_c"}),
 *     @ORM\Index(name="fk_users_id_aa7968df", columns={"created_by"}),
 *     @ORM\Index(name="fk_users_id_2ebb459b", columns={"modified_user_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="dashboards",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_dashboard",
 *              joinColumns=@ORM\JoinColumn(name="dashboard_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity(repositoryClass="ExEss\Cms\Repository\DashboardRepository")
 */
class Dashboard extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="type_c", type="enum_dashboard_type", nullable=true, options={"default"="DEFAULT"})
     */
    private ?string $type = DashboardType::DEFAULT;

    /**
     * @ORM\Column(name="key_c", type="string", length=255, nullable=true)
     */
    private ?string $key = null;

    /**
     * @ORM\Column(name="menu_sort_c", type="string", length=255, nullable=true)
     */
    private ?string $menuSort = null;

    /**
     * @ORM\Column(name="main_record_type_c", type="string", length=255, nullable=true)
     */
    private ?string $mainRecordType = null;

    /**
     * @ORM\Column(name="filters_listkey", type="string", length=255, nullable=true)
     */
    private ?string $filtersListKey = null;

    /**
     * @ORM\ManyToOne(targetEntity="GridTemplate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="grid_gridtemplates_id_c", referencedColumnName="id")
     * })
     */
    private ?GridTemplate $gridTemplate = null;

    /**
     * @ORM\ManyToMany(targetEntity="Property", inversedBy="dashboards")
     * @ORM\JoinTable(name="dash_dashboard_dash_dashboardproperties_c")
     * @Auditable
     * @var Collection|Property[]
     */
    private Collection $properties;

    /**
     * @ORM\ManyToMany(targetEntity="Menu", mappedBy="dashboards")
     * @ORM\OrderBy({"order" = "asc"})
     * @var Collection|Menu[]
     */
    private Collection $menus;

    /**
     * @ORM\ManyToOne(targetEntity="DashboardMenu", inversedBy="dashboards")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?DashboardMenu $dashboardMenu = null;

    /**
     * @ORM\ManyToOne(targetEntity="FindSearch", inversedBy="dashboards")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?FindSearch $search = null;

    /**
     * @ORM\ManyToOne(targetEntity="Filter", inversedBy="dashboards")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Filter $filter = null;

    public function __construct()
    {
        $this->properties = new ArrayCollection();
        $this->menus = new ArrayCollection();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getMenuSort(): ?string
    {
        return $this->menuSort;
    }

    public function getMainRecordType(): ?string
    {
        return $this->mainRecordType;
    }

    public function getFiltersListKey(): ?string
    {
        return $this->filtersListKey;
    }

    public function getGridTemplate(): ?GridTemplate
    {
        return $this->gridTemplate;
    }

    /**
     * @return Collection|Property[]
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    /**
     * @return Collection|Menu[]
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function getDashboardMenu(): ?DashboardMenu
    {
        return $this->dashboardMenu;
    }

    public function getSearch(): ?FindSearch
    {
        return $this->search;
    }

    public function getFilter(): ?Filter
    {
        return $this->filter;
    }

    public function setGridTemplate(GridTemplate $gridTemplate): void
    {
        $this->gridTemplate = $gridTemplate;
    }
}
