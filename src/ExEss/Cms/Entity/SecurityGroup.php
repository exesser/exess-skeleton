<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Component\Doctrine\Mapping\Annotation\Auditable;
use ExEss\Cms\Doctrine\Type\SecurityGroupType;
use ExEss\Cms\Doctrine\Type\UserStatus;
use ExEss\Cms\Entity\Base\Entity;

/**
 * @ORM\Table(name="securitygroups", indexes={
 *     @ORM\Index(name="fk_users_id_06b55379", columns={"assigned_user_id"}),
 *     @ORM\Index(name="fk_users_id_5fc842bf", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_26ee194a", columns={"created_by"})
 * })
 * @ORM\Entity
 */
class SecurityGroup extends Entity
{
    /**
     * @ORM\Column(name="external_c", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $external = false;

    /**
     * @ORM\Column(name="main_groups_c", type="enum_security_group_type", nullable=true, options={
     *     "default"="THIRD_PARTY"
     * })
     */
    private ?string $type = SecurityGroupType::THIRD_PARTY;

    /**
     * @ORM\Column(name="reliable_c", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $reliable = false;

    /**
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private string $code;

    /**
     * @ORM\Column(name="status", type="enum_user_status", nullable=false, options={"default"="Active"})
     */
    private string $status = UserStatus::ACTIVE;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="assigned_user_id", referencedColumnName="id", nullable=true)
     * })
     */
    private ?User $assignedUser;

    /**
     * @ORM\ManyToMany(targetEntity="AclRole", inversedBy="groups")
     * @ORM\JoinTable(name="securitygroups_acl_roles")
     * @Auditable
     * @var Collection|AclRole[]
     */
    private Collection $roles;

    /**
     * @ORM\ManyToMany(targetEntity="Dashboard", mappedBy="securityGroups")
     * @var Collection|Dashboard[]
     */
    private Collection $dashboards;

    /**
     * @ORM\ManyToMany(targetEntity="Property", mappedBy="securityGroups")
     * @var Collection|Property[]
     */
    private Collection $properties;

    /**
     * @ORM\ManyToMany(targetEntity="Filter", mappedBy="securityGroups")
     * @var Collection|Filter[]
     */
    private Collection $filters;

    /**
     * @ORM\ManyToMany(targetEntity="FlowAction", mappedBy="securityGroups")
     * @var Collection|FlowAction[]
     */
    private Collection $flowActions;

    /**
     * @ORM\ManyToMany(targetEntity="ListTopBar", mappedBy="securityGroups")
     * @var Collection|ListTopBar[]
     */
    private Collection $listTopBars;

    /**
     * @ORM\ManyToMany(targetEntity="Validator", mappedBy="securityGroups")
     * @var Collection|Validator[]
     */
    private Collection $validators;

    /**
     * @ORM\ManyToMany(targetEntity="ListRowAction", mappedBy="securityGroups")
     * @var Collection|ListRowAction[]
     */
    private Collection $listRowActions;

    /**
     * @ORM\ManyToMany(targetEntity="FilterField", mappedBy="securityGroups")
     * @var Collection|FilterField[]
     */
    private Collection $filterFields;

    /**
     * @ORM\ManyToMany(targetEntity="ListDynamic", mappedBy="securityGroups")
     * @var Collection|ListDynamic[]
     */
    private Collection $lists;

    /**
     * @ORM\ManyToMany(targetEntity="Flow", mappedBy="securityGroups")
     * @var Collection|Flow[]
     */
    private Collection $flows;

    /**
     * @ORM\ManyToMany(targetEntity="ConditionalMessage", mappedBy="securityGroups")
     * @var Collection|ConditionalMessage[]
     */
    private Collection $conditionalMessages;

    /**
     * @ORM\ManyToMany(targetEntity="FlowStepLink", mappedBy="securityGroups")
     * @var Collection|FlowStepLink[]
     */
    private Collection $flowStepLinks;

    /**
     * @ORM\ManyToMany(targetEntity="FilterFieldGroup", mappedBy="securityGroups")
     * @var Collection|FilterFieldGroup[]
     */
    private Collection $filterFieldGroups;

    /**
     * @ORM\ManyToMany(targetEntity="ListCellLink", mappedBy="securityGroups")
     * @var Collection|ListCellLink[]
     */
    private Collection $listCellLinks;

    /**
     * @ORM\ManyToMany(targetEntity="FindSearch", mappedBy="securityGroups")
     * @var Collection|FindSearch[]
     */
    private Collection $findSearches;

    /**
     * @ORM\ManyToMany(targetEntity="DashboardMenu", mappedBy="securityGroups")
     * @var Collection|DashboardMenu[]
     */
    private Collection $dashboardMenus;

    /**
     * @ORM\ManyToMany(targetEntity="GridPanel", mappedBy="securityGroups")
     * @var Collection|GridPanel[]
     */
    private Collection $gridPanels;

    /**
     * @ORM\ManyToMany(targetEntity="FlowField", mappedBy="securityGroups")
     * @var Collection|FlowField[]
     */
    private Collection $flowFields;

    /**
     * @ORM\ManyToMany(targetEntity="ListTopAction", mappedBy="securityGroups")
     * @var Collection|ListTopAction[]
     */
    private Collection $listTopActions;

    /**
     * @ORM\ManyToMany(targetEntity="ListSortingOption", mappedBy="securityGroups")
     * @var Collection|ListSortingOption[]
     */
    private Collection $listSortingOptions;

    /**
     * @ORM\ManyToMany(targetEntity="DashboardMenuAction", mappedBy="securityGroups")
     * @var Collection|DashboardMenuAction[]
     */
    private Collection $dashboardMenuActions;

    /**
     * @ORM\ManyToMany(targetEntity="GridTemplate", mappedBy="securityGroups")
     * @var Collection|GridTemplate[]
     */
    private Collection $gridTemplates;

    /**
     * @ORM\ManyToMany(targetEntity="DashboardMenuActionGroup", mappedBy="securityGroups")
     * @var Collection|DashboardMenuActionGroup[]
     */
    private Collection $dashboardMenuActionGroups;

    /**
     * @ORM\ManyToMany(targetEntity="Menu", mappedBy="securityGroups")
     * @var Collection|Menu[]
     */
    private Collection $menus;

    /**
     * @ORM\ManyToMany(targetEntity="SecurityGroupApi", mappedBy="securityGroups")
     * @var Collection|SecurityGroupApi[]
     */
    private Collection $apis;

    /**
     * @ORM\ManyToMany(targetEntity="ExternalObjectLink", mappedBy="securityGroups")
     * @var Collection|ExternalObjectLink[]
     */
    private Collection $externalObjectLinks;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->dashboards = new ArrayCollection();
        $this->properties = new ArrayCollection();
        $this->filters = new ArrayCollection();
        $this->flowActions = new ArrayCollection();
        $this->listTopBars = new ArrayCollection();
        $this->validators = new ArrayCollection();
        $this->listRowActions = new ArrayCollection();
        $this->filterFields = new ArrayCollection();
        $this->lists = new ArrayCollection();
        $this->flows = new ArrayCollection();
        $this->conditionalMessages = new ArrayCollection();
        $this->flowStepLinks = new ArrayCollection();
        $this->filterFieldGroups = new ArrayCollection();
        $this->listCellLinks = new ArrayCollection();
        $this->findSearches = new ArrayCollection();
        $this->dashboardMenus = new ArrayCollection();
        $this->gridPanels = new ArrayCollection();
        $this->flowFields = new ArrayCollection();
        $this->listTopActions = new ArrayCollection();
        $this->listSortingOptions = new ArrayCollection();
        $this->dashboardMenuActions = new ArrayCollection();
        $this->gridTemplates = new ArrayCollection();
        $this->dashboardMenuActionGroups = new ArrayCollection();
        $this->menus = new ArrayCollection();
        $this->apis = new ArrayCollection();
        $this->externalObjectLinks = new ArrayCollection();
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }
}
