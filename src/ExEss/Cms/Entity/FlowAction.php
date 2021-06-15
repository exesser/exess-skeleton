<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="flw_actions", indexes={
 *     @ORM\Index(name="fk_users_id_cc8489c9", columns={"created_by"}),
 *     @ORM\Index(name="fk_users_id_244b2f9c", columns={"modified_user_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="flowActions",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_flow_action",
 *              joinColumns=@ORM\JoinColumn(name="flow_action_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity(repositoryClass="ExEss\Cms\Repository\FlowActionRepository")
 */
class FlowAction extends Entity
{
    use SecurityGroups;

    public const KEY_MODAL_TO_REMOVE_RECOVERY_GUIDANCE_DATA = 'remove_recovery_guidance_data';
    public const KEY_MODAL_VALIDATION_ERROR_FLOW = 'validation_error';
    public const ACTION_MODAL_TO_GF_RECOVERY = 'modal_to_gf_recovery';

    public const USED_ACTIONS = [
        self::ACTION_MODAL_TO_GF_RECOVERY,
        self::KEY_MODAL_TO_REMOVE_RECOVERY_GUIDANCE_DATA,
        self::KEY_MODAL_VALIDATION_ERROR_FLOW,
    ];

    /**
     * @ORM\Column(name="guid", type="string", length=255, nullable=true)
     */
    private ?string $guid = null;

    /**
     * @ORM\Column(name="json", type="json", nullable=true)
     */
    private ?array $json = null;

    /**
     * @ORM\OneToMany(targetEntity="ListRowAction", mappedBy="flowAction")
     * @var Collection|ListRowAction[]
     */
    private Collection $rowActions;

    /**
     * @ORM\OneToMany(targetEntity="FlowField", mappedBy="flowAction")
     * @var Collection|FlowField[]
     */
    private Collection $fields;

    public function __construct()
    {
        $this->rowActions = new ArrayCollection();
        $this->fields = new ArrayCollection();
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function getJson(): ?array
    {
        return $this->json;
    }
}
