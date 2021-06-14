<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Component\Doctrine\Mapping\Annotation\Auditable;
use ExEss\Cms\Doctrine\Type\Validator as EnabledValidator;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="flw_guidancefieldvalidators", indexes={
 *     @ORM\Index(name="fk_users_id_eaaeca80", columns={"created_by"}),
 *     @ORM\Index(name="fk_users_id_b65e1ce5", columns={"modified_user_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="validators",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_validator",
 *              joinColumns=@ORM\JoinColumn(name="validator_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class Validator extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="validator_value", type="text", length=65535, nullable=true)
     */
    private ?string $value = null;

    /**
     * @ORM\Column(name="validator_type", type="enum_validator_type", nullable=true)
     */
    private ?string $type = null;

    /**
     * @ORM\Column(name="validator_min", type="integer", nullable=true)
     */
    private ?int $min = null;

    /**
     * @ORM\Column(name="validator_max", type="integer", nullable=true)
     */
    private ?int $max = null;

    /**
     * @ORM\Column(name="validator", type="enum_validator", nullable=true, options={"default"="NotBlank"})
     */
    private ?string $validator = EnabledValidator::NOT_BLANK;

    /**
     * @ORM\Column(name="validator_field", type="string", length=255, nullable=true, options={"default"="__self__"})
     */
    private ?string $field = '__self__';

    /**
     * @ORM\Column(name="validator_mode", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $mode = false;

    /**
     * @ORM\Column(name="validation_group", type="string", length=255, nullable=true)
     */
    private ?string $validationGroup = null;

    /**
     * @ORM\Column(name="validator_mutator", type="enum_validator_mutator", nullable=true)
     */
    private ?string $mutator = null;

    /**
     * @ORM\Column(name="custom_error_message", type="string", length=255, nullable=true)
     */
    private ?string $customErrorMessage = null;

    /**
     * @ORM\Column(name="validator_maxfilesize", type="string", length=255, nullable=true)
     */
    private ?string $maxFileSize = null;

    /**
     * @ORM\Column(name="show_on_top", type="boolean", nullable=false)
     */
    private bool $showOnTop;

    /**
     * @ORM\Column(name="and_not_null", type="boolean", nullable=false)
     */
    private bool $andNotNull;

    /**
     * @ORM\ManyToMany(targetEntity="ConditionalMessage", mappedBy="conditions")
     * @var Collection|ConditionalMessage[]
     */
    private Collection $conditionalMessages;

    /**
     * @ORM\ManyToMany(targetEntity="GridPanel", mappedBy="conditions")
     * @var Collection|GridPanel[]
     */
    private Collection $panels;

    /**
     * @ORM\ManyToMany(targetEntity="FlowField", mappedBy="validators")
     * @var Collection|FlowField[]
     */
    private Collection $fields;

    /**
     * @ORM\ManyToMany(targetEntity="Validator", mappedBy="parents")
     * @var Collection|Validator[]
     */
    private Collection $children;

    /**
     * @ORM\ManyToMany(targetEntity="Validator", inversedBy="children")
     * @ORM\JoinTable(
     *     name="flw_guidancefieldsvalidators_conditions",
     *     joinColumns={
     *          @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="child_id", referencedColumnName="id")
     *     }
     * )
     * @Auditable
     * @var Collection|Validator[]
     */
    private Collection $parents;

    public function __construct()
    {
        $this->conditionalMessages = new ArrayCollection();
        $this->panels = new ArrayCollection();
        $this->fields = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->parents = new ArrayCollection();
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function getValidator(): ?string
    {
        return $this->validator;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function getMode(): ?bool
    {
        return $this->mode;
    }

    public function getValidationGroup(): ?string
    {
        return $this->validationGroup;
    }

    public function getMutator(): ?string
    {
        return $this->mutator;
    }

    public function getCustomErrorMessage(): ?string
    {
        return $this->customErrorMessage;
    }

    public function getMaxFileSize(): ?string
    {
        return $this->maxFileSize;
    }

    public function isShowOnTop(): ?bool
    {
        return $this->showOnTop;
    }

    public function isAndNotNull(): bool
    {
        return $this->andNotNull;
    }

    /**
     * @return Collection|ConditionalMessage[]
     */
    public function getConditionalMessages(): Collection
    {
        return $this->conditionalMessages;
    }

    /**
     * @return Collection|GridPanel[]
     */
    public function getPanels(): Collection
    {
        return $this->panels;
    }

    /**
     * @return Collection|FlowField[]
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    /**
     * @return Collection|Validator[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @return Collection|Validator[]
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }
}
