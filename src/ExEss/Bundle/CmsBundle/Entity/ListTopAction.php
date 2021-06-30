<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\CmsBundle\Doctrine\Type\ActionType;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;
use ExEss\Bundle\CmsBundle\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="list_top_action", indexes={
 *     @ORM\Index(name="fk_users_id_0563b06e", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_flw_actions_id_e466e703", columns={"flw_actions_id_c"}),
 *     @ORM\Index(name="fk_users_id_de98942b", columns={"created_by"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="listTopActions",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_list_top_action",
 *              joinColumns=@ORM\JoinColumn(name="list_top_action_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class ListTopAction extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="type", type="enum_action_type", nullable=true, options={"default"="CALLBACK"})
     */
    private ?string $type = ActionType::CALLBACK;

    /**
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     */
    private ?string $icon = null;

    /**
     * @ORM\Column(name="action_name", type="string", length=255, nullable=true)
     */
    private ?string $actionName = null;

    /**
     * @ORM\Column(name="order_c", type="integer", nullable=true, options={"default"="10"})
     */
    private ?int $order = 10;

    /**
     * @ORM\Column(name="params_c", type="json", nullable=true)
     */
    private ?array $params = null;

    /**
     * @ORM\Column(name="key_c", type="string", length=255, nullable=true)
     */
    private ?string $key = null;

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
     * @ORM\ManyToMany(targetEntity="ListTopBar", mappedBy="actions")
     * @var Collection|ListTopBar[]
     */
    private Collection $topBars;

    public function __construct()
    {
        $this->topBars = new ArrayCollection();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getActionName(): ?string
    {
        return $this->actionName;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function getKey(): ?string
    {
        return $this->key;
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

    /**
     * @return Collection|ListTopBar[]
     */
    public function getTopBars(): Collection
    {
        return $this->topBars;
    }
}
