<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\CmsBundle\Doctrine\Type\ActionType;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;
use ExEss\Bundle\CmsBundle\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="list_row_action", indexes={
 *     @ORM\Index(name="fk_users_id_e9284788", columns={"created_by"}),
 *     @ORM\Index(name="fk_users_id_a5f43d19", columns={"modified_user_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="listRowActions",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_list_row_action",
 *              joinColumns=@ORM\JoinColumn(name="list_row_action_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class ListRowAction extends Entity
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
     * @ORM\Column(name="conditionsenabled_c", type="json", nullable=true)
     */
    private ?array $enableConditions = null;

    /**
     * @ORM\Column(name="conditions_hide_c", type="json", nullable=true)
     */
    private ?array $hideConditions = null;

    /**
     * @ORM\Column(name="order_c", type="integer", nullable=true, options={"default"="10"})
     */
    private ?int $order = 10;

    /**
     * @ORM\Column(name="params_c", type="json", nullable=true)
     */
    private ?array $params = null;

    /**
     * @ORM\ManyToOne(targetEntity="FlowAction", inversedBy="rowActions")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?FlowAction $flowAction = null;

    /**
     * @ORM\ManyToOne(targetEntity="ListRowBar", inversedBy="rowActions")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ListRowBar $rowBar = null;

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

    public function getEnableConditions(): ?array
    {
        return $this->enableConditions;
    }

    public function getHideConditions(): ?array
    {
        return $this->hideConditions;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function getFlowAction(): ?FlowAction
    {
        return $this->flowAction;
    }

    public function getRowBar(): ?ListRowBar
    {
        return $this->rowBar;
    }
}
