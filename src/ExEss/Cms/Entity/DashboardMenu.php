<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Component\Doctrine\Mapping\Annotation\Auditable;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="dash_dashboardmenu", indexes={
 *     @ORM\Index(name="fk_users_id_817d915c", columns={"created_by"}),
 *     @ORM\Index(name="fk_users_id_85910a54", columns={"modified_user_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="dashboardMenus",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_dashboard_menu",
 *              joinColumns=@ORM\JoinColumn(name="dashboard_menu_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class DashboardMenu extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\ManyToMany(targetEntity="DashboardMenuActionGroup", inversedBy="menus")
     * @ORM\JoinTable(name="dash_dashboardmenu_dash_dashboardmenuactiongroup_1_c")
     * @Auditable
     * @var Collection|DashboardMenuActionGroup[]
     */
    private Collection $groups;

    /**
     * @ORM\ManyToMany(targetEntity="DashboardMenuAction", inversedBy="menus")
     * @ORM\JoinTable(name="dash_dashboardmenu_dash_menuactions_1_c")
     * @Auditable
     * @var Collection|DashboardMenuAction[]
     */
    private Collection $actions;

    /**
     * @ORM\OneToMany(targetEntity="Dashboard", mappedBy="dashboardMenu")
     * @var Collection|Dashboard[]
     */
    private Collection $dashboards;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->dashboards = new ArrayCollection();
    }

    /**
     * @return Collection|DashboardMenuActionGroup[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * @return Collection|DashboardMenuAction[]
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    /**
     * @return Collection|Dashboard[]
     */
    public function getDashboards(): Collection
    {
        return $this->dashboards;
    }
}
