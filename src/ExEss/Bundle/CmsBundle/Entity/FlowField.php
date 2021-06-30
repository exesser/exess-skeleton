<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\DoctrineExtensionsBundle\Mapping\Annotation\Auditable;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;
use ExEss\Bundle\CmsBundle\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="flw_guidancefields", indexes={
 *     @ORM\Index(name="fk_users_id_a2a4c0e6", columns={"created_by"}),
 *     @ORM\Index(name="idx_field_type", columns={"field_type"}),
 *     @ORM\Index(name="fk_users_id_1bd9c57c", columns={"modified_user_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="flowFields",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_flow_field",
 *              joinColumns=@ORM\JoinColumn(name="flow_field_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class FlowField extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="field_id", type="text", length=65535, nullable=true)
     */
    private ?string $fieldId = null;

    /**
     * @ORM\Column(name="field_label", type="string", length=255, nullable=true)
     */
    private ?string $label = null;

    /**
     * @ORM\Column(name="field_default", type="text", length=65535, nullable=true)
     */
    private ?string $default = null;

    /**
     * @ORM\Column(name="field_type", type="enum_flow_field_type", nullable=true)
     */
    private ?string $type = null;

    /**
     * @ORM\Column(name="field_generatebyserver", type="boolean", nullable=true, options={"default"="1"})
     */
    private ?bool $generateByServer = true;

    /**
     * @ORM\Column(name="field_module", type="string", length=255, nullable=true)
     */
    private ?string $module = null;

    /**
     * @ORM\Column(name="field_modulefield", type="string", length=255, nullable=true)
     */
    private ?string $moduleField = null;

    /**
     * @ORM\Column(name="field_generatetype", type="enum_generated_field_type", nullable=true)
     */
    private ?string $generatedType = null;

    /**
     * @ORM\Column(name="field_hideexpression", type="text", length=65535, nullable=true)
     */
    private ?string $hideExpression = null;

    /**
     * @ORM\Column(name="field_disableexpression", type="text", length=65535, nullable=true)
     */
    private ?string $disableExpression = null;

    /**
     * @ORM\Column(name="field_multiple", type="boolean", nullable=true)
     */
    private ?bool $multiple = null;

    /**
     * @ORM\Column(name="field_fieldgroup", type="string", length=255, nullable=true)
     */
    private ?string $fieldGroup = null;

    /**
     * @ORM\Column(name="field_order", type="integer", nullable=true, options={"default"="100"})
     */
    private ?int $order = 100;

    /**
     * @ORM\Column(name="field_action_json", type="json", nullable=true)
     */
    private ?array $actionJson = null;

    /**
     * @ORM\Column(name="field_hasborder", type="boolean", nullable=true, options={"default"="1"})
     */
    private ?bool $hasBorder = true;

    /**
     * @ORM\Column(name="field_orientation", type="enum_field_orientation", nullable=true)
     */
    private ?string $orientation = null;

    /**
     * @ORM\Column(name="field_enum_values", type="json", nullable=true)
     */
    private ?array $enumValues = null;

    /**
     * @ORM\Column(name="field_fieldexpression", type="text", length=65535, nullable=true)
     */
    private ?string $fieldExpression = null;

    /**
     * @ORM\Column(name="field_upload_validation", type="string", length=255, nullable=true)
     */
    private ?string $uploadValidation = null;

    /**
     * @ORM\Column(name="field_custom", type="json", nullable=true)
     */
    private ?array $custom = null;

    /**
     * @ORM\Column(name="field_read_only", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $readOnly = false;

    /**
     * @ORM\Column(name="required_c", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $required = false;

    /**
     * @ORM\Column(name="field_no_backend_interaction", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $noBackendInteraction = false;

    /**
     * @ORM\Column(name="field_valueexpression", type="text", length=65535, nullable=true)
     */
    private ?string $valueExpression = null;

    /**
     * @ORM\Column(name="field_auto_select_suggestions", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $autoSelectSuggestions = false;

    /**
     * @ORM\Column(name="remove_when_empty", type="boolean", nullable=true)
     */
    private ?bool $removeWhenEmpty = null;

    /**
     * @ORM\Column(name="field_overwrite_value", type="string", length=1024, nullable=true)
     */
    private ?string $overwriteValue = null;

    /**
     * @ORM\ManyToMany(targetEntity="FlowStep", inversedBy="fields")
     * @ORM\JoinTable(name="flw_guidancefields_flw_flowsteps_c")
     * @Auditable
     * @var Collection|FlowStep[]
     */
    private Collection $steps;

    /**
     * @ORM\ManyToMany(targetEntity="Validator", inversedBy="fields")
     * @ORM\JoinTable(name="flw_guidancefields_flw_guidancefieldvalidators_1_c")
     * @ORM\OrderBy({"validationGroup" = "asc"})
     * @Auditable
     * @var Collection|Validator[]
     */
    private Collection $validators;

    /**
     * @ORM\ManyToOne(targetEntity="FlowAction", inversedBy="fields")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?FlowAction $flowAction = null;

    public function __construct()
    {
        $this->validators = new ArrayCollection();
        $this->steps = new ArrayCollection();
    }

    public function getFieldId(): ?string
    {
        return $this->fieldId;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getGenerateByServer(): bool
    {
        return (bool) $this->generateByServer;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function getModuleField(): ?string
    {
        return $this->moduleField;
    }

    public function getGeneratedType(): ?string
    {
        return $this->generatedType;
    }

    public function getHideExpression(): ?string
    {
        return $this->hideExpression;
    }

    public function getDisableExpression(): ?string
    {
        return $this->disableExpression;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->multiple;
    }

    public function getFieldGroup(): ?string
    {
        return $this->fieldGroup;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function getActionJson(): ?array
    {
        return $this->actionJson;
    }

    public function getHasBorder(): bool
    {
        return (bool) $this->hasBorder;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function getEnumValues(): ?array
    {
        return $this->enumValues;
    }

    public function getFieldExpression(): ?string
    {
        return $this->fieldExpression;
    }

    public function getUploadValidation(): ?string
    {
        return $this->uploadValidation;
    }

    public function getCustom(): ?array
    {
        return $this->custom;
    }

    public function getReadOnly(): bool
    {
        return (bool) $this->readOnly;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function getNoBackendInteraction(): bool
    {
        return (bool) $this->noBackendInteraction;
    }

    public function getValueExpression(): ?string
    {
        return $this->valueExpression;
    }

    public function getAutoSelectSuggestions(): bool
    {
        return (bool) $this->autoSelectSuggestions;
    }

    public function isRemoveWhenEmpty(): bool
    {
        return (bool) $this->removeWhenEmpty;
    }

    public function getOverwriteValue(): ?string
    {
        return $this->overwriteValue;
    }

    /**
     * @return Collection|FlowStep[]
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    /**
     * @return Collection|Validator[]
     */
    public function getValidators(): Collection
    {
        return $this->validators;
    }

    public function getFlowAction(): ?FlowAction
    {
        return $this->flowAction;
    }

    public function isRequired(): bool
    {
        /** @var Collection|Validator[] $notBlankValidators */
        $notBlankValidators = $this->getValidators()->filter(function (Validator $validator) {
            return $validator->getValidator() === \ExEss\Bundle\CmsBundle\Doctrine\Type\Validator::NOT_BLANK;
        });

        foreach ($notBlankValidators as $validator) {
            if ($validator->getChildren()->count() === 0) {
                return true;
            }
        }

        return false;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function setFieldId(string $fieldId): void
    {
        $this->fieldId = $fieldId;
    }

    public function setHideExpression(string $hideExpression): void
    {
        $this->hideExpression = $hideExpression;
    }
}
