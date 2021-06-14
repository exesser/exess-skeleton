<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="dash_menuactions", indexes={
 *     @ORM\Index(name="fk_flw_actions_id_29cdc032", columns={"flw_actions_id_c"}),
 *     @ORM\Index(name="fk_users_id_44ee9b96", columns={"created_by"}),
 *     @ORM\Index(name="fk_users_id_b842ba88", columns={"modified_user_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="dashboardMenuActions",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_dashboard_menu_action",
 *              joinColumns=@ORM\JoinColumn(name="dashboard_menu_action_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class DashboardMenuAction extends Entity
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
     * @ORM\Column(name="icon_c", type="string", length=100, nullable=true)
     */
    private ?string $icon = null;

    /**
     * @ORM\Column(name="params_c", type="json", nullable=true)
     */
    private ?array $params = null;

    /**
     * @ORM\Column(name="conditionsenabled_c", type="json", nullable=true)
     */
    private ?array $enableConditions = null;

    /**
     * @ORM\Column(name="conditions_hide_c", type="json", nullable=true)
     */
    private ?array $hideConditions = null;

    /**
     * @ORM\ManyToOne(targetEntity="FlowAction")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="flw_actions_id_c", referencedColumnName="id")
     * })
     */
    private FlowAction $flowAction;

    /**
     * @ORM\ManyToMany(targetEntity="DashboardMenu", mappedBy="actions")
     * @var Collection|DashboardMenu[]
     */
    private Collection $menus;

    /**
     * @ORM\ManyToMany(targetEntity="DashboardMenuActionGroup", mappedBy="actions")
     * @var Collection|DashboardMenuActionGroup[]
     */
    private Collection $groups;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
        $this->groups = new ArrayCollection();
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

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function getEnableConditions(): ?array
    {
        return $this->enableConditions;
    }

    public function getHideConditions(): ?array
    {
        return $this->hideConditions;
    }

    public function getFlowAction(): FlowAction
    {
        return $this->flowAction;
    }
}
