<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Doctrine\Type\MessageDomain;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Bundle\DoctrineExtensionsBundle\Mapping\Annotation\Auditable;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="conditionalmessage", indexes={
 *     @ORM\Index(name="fk_users_id_94dc668b", columns={"assigned_user_id"}),
 *     @ORM\Index(name="fk_users_id_bca73b85", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_b8a72bb4", columns={"created_by"}),
 *     @ORM\Index(name="key_domain", columns={"domain"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="conditionalMessages",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_conditional_message",
 *              joinColumns=@ORM\JoinColumn(name="conditional_message_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class ConditionalMessage extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="domain", type="enum_message_domain", nullable=true, options={"default"="sidebar"})
     */
    private ?string $domain = MessageDomain::DEFAULT;

    /**
     * @ORM\Column(name="action_c", type="string", length=255, nullable=true)
     */
    private ?string $action = null;

    /**
     * @ORM\Column(name="icon_c", type="string", length=255, nullable=true)
     */
    private ?string $icon = null;

    /**
     * @ORM\Column(name="record_type", type="string", length=255, nullable=true)
     */
    private ?string $recordType = null;

    /**
     * @ORM\Column(name="record_id", type="string", length=255, nullable=true)
     */
    private ?string $recordId = null;

    /**
     * @ORM\Column(name="priority_c", type="integer", nullable=true, options={"default"="1"})
     */
    private ?int $priority = 1;

    /**
     * @ORM\Column(name="description_params", type="json", nullable=true)
     */
    private ?array $descriptionParams = null;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="assigned_user_id", referencedColumnName="id")
     * })
     */
    private User $assignedUser;

    /**
     * @ORM\ManyToMany(targetEntity="Validator", inversedBy="conditionalMessages")
     * @ORM\JoinTable(name="conditional_message_validators")
     * @Auditable
     * @var Collection|Validator[]
     */
    private Collection $conditions;

    public function __construct()
    {
        $this->conditions = new ArrayCollection();
    }
}
