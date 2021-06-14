<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="flw_flowstepslink", indexes={
 *     @ORM\Index(name="fk_users_id_8367cbfd", columns={"created_by"}),
 *     @ORM\Index(name="fk_users_id_91522fa7", columns={"modified_user_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="flowStepLinks",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_flow_step_link",
 *              joinColumns=@ORM\JoinColumn(name="flow_step_link_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class FlowStepLink extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="order_c", type="integer", nullable=true, options={"default"="1"})
     */
    private ?int $order = 1;

    /**
     * @ORM\ManyToOne(targetEntity="Flow", inversedBy="stepLinks")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Flow $flow = null;

    /**
     * @ORM\ManyToOne(targetEntity="FlowStep", inversedBy="stepLinks")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?FlowStep $flowStep = null;

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): void
    {
        $this->order = $order;
    }

    public function getFlow(): ?Flow
    {
        return $this->flow;
    }

    public function getFlowStep(): ?FlowStep
    {
        return $this->flowStep;
    }
}
