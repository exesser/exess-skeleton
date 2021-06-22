<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\DoctrineExtensionsBundle\Mapping\Annotation\Auditable;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="dash_dashboardmenuactiongroup", indexes={
 *     @ORM\Index(name="fk_users_id_40175142", columns={"created_by"}),
 *     @ORM\Index(name="fk_users_id_16e1cd88", columns={"modified_user_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="dashboardMenuActionGroups",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_dashboard_menu_action_group",
 *              joinColumns=@ORM\JoinColumn(name="dashboard_menu_action_group_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class DashboardMenuActionGroup extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="label_c", type="string", length=100, nullable=true)
     */
    private ?string $label = null;

    /**
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    private ?int $sortOrder = null;

    /**
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     */
    private ?string $icon = null;

    /**
     * @ORM\Column(name="class", type="string", length=255, nullable=true)
     */
    private ?string $class = null;

    /**
     * @ORM\Column(name="conditions_hide_c", type="json", nullable=true)
     */
    private ?array $hideConditions = null;

    /**
     * @ORM\ManyToMany(targetEntity="DashboardMenu", mappedBy="groups")
     * @var Collection|DashboardMenu[]
     */
    private Collection $menus;

    /**
     * @ORM\ManyToMany(targetEntity="DashboardMenuActionGroup", mappedBy="parents")
     * @var Collection|DashboardMenuActionGroup[]
     */
    private Collection $children;

    /**
     * @ORM\ManyToMany(targetEntity="DashboardMenuActionGroup", inversedBy="children")
     * @ORM\JoinTable(
     *     name="dash_menuactiongroup_x_dash_menuactiongroup",
     *     joinColumns={
     *          @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="child_id", referencedColumnName="id")
     *     }
     * )
     * @Auditable
     * @var Collection|DashboardMenuActionGroup[]
     */
    private Collection $parents;

    /**
     * @ORM\ManyToMany(targetEntity="DashboardMenuAction", inversedBy="groups")
     * @ORM\JoinTable(name="dash_dashboardmenuactiongroup_dash_menuactions")
     * @Auditable
     * @var Collection|DashboardMenuAction[]
     */
    private Collection $actions;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function getHideConditions(): ?array
    {
        return $this->hideConditions;
    }

    /**
     * @return Collection|DashboardMenu[]
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    /**
     * @return Collection|DashboardMenuActionGroup[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @return Collection|DashboardMenuActionGroup[]
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }

    /**
     * @return Collection|DashboardMenuAction[]
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }
}
