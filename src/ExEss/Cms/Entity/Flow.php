<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Doctrine\Type\FlowType;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;
use ExEss\Cms\Exception\NotFoundException;

/**
 * @ORM\Table(name="flw_flows", indexes={
 *     @ORM\Index(name="fk_users_id_a08dfff7", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_53b16522", columns={"created_by"}),
 *     @ORM\Index(name="idx_key_c", columns={"key_c"}),
 *     @ORM\Index(name="fk_flw_actions_id_a235cd23", columns={"action_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="flows",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_flow",
 *              joinColumns=@ORM\JoinColumn(name="flow_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity(repositoryClass="ExEss\Cms\Repository\FlowRepository")
 */
class Flow extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="key_c", type="string", length=255, nullable=true)
     */
    private ?string $key = null;

    /**
     * @ORM\Column(name="type_c", type="enum_flow_type", nullable=true, options={"default"="STANDARD"})
     */
    private ?string $type = FlowType::STANDARD;

    /**
     * @ORM\Column(name="base_object_c", type="string", length=150, nullable=true)
     */
    private ?string $baseObject = null;

    /**
     * @ORM\Column(name="loading_message_c", type="string", length=150, nullable=true)
     */
    private ?string $loadingMessage = null;

    /**
     * @ORM\Column(name="error_message", type="text", length=65535, nullable=true)
     */
    private ?string $errorMessage = null;

    /**
     * @ORM\Column(name="external", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $external = false;

    /**
     * @ORM\Column(name="label_c", type="string", length=255, nullable=true, options={"default"="1"})
     */
    private ?string $label = '1';

    /**
     * @ORM\Column(name="use_api_label_c", type="boolean", nullable=false)
     */
    private bool $useApiLabel;

    /**
     * @ORM\Column(name="is_config", type="boolean", nullable=false)
     */
    private bool $isConfig;

    /**
     * @ORM\ManyToOne(targetEntity="FlowAction")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     * })
     */
    private FlowAction $action;

    /**
     * @ORM\OneToMany(targetEntity="GridPanel", mappedBy="flow")
     * @var Collection|GridPanel[]
     */
    private Collection $gridPanels;

    /**
     * @ORM\OneToMany(targetEntity="FlowStepLink", mappedBy="flow")
     * @ORM\OrderBy({"order" = "asc"})
     * @var Collection|FlowStepLink[]
     */
    private Collection $stepLinks;

    /**
     * @var Collection|FlowStep[]
     */
    private ?Collection $steps = null;

    /**
     * @var Collection|FlowField[]
     */
    private ?Collection $fields = null;

    public function __construct()
    {
        $this->stepLinks = new ArrayCollection();
        $this->gridPanels = new ArrayCollection();
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getBaseObject(): ?string
    {
        return $this->baseObject;
    }

    public function getLoadingMessage(): ?string
    {
        return $this->loadingMessage;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function isExternal(): ?bool
    {
        return $this->external;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getUseApiLabel(): ?bool
    {
        return $this->useApiLabel;
    }

    public function getIsConfig(): bool
    {
        return $this->isConfig;
    }

    public function getAction(): ?FlowAction
    {
        return $this->action;
    }

    /**
     * @return Collection|FlowStepLink[]
     */
    public function getStepLinks(): Collection
    {
        return $this->stepLinks;
    }

    /**
     * @return Collection|FlowStep[]
     */
    public function getSteps(): Collection
    {
        if ($this->steps === null) {
            $this->steps = new ArrayCollection();
            foreach ($this->getStepLinks() as $stepLink) {
                $this->steps[] = $stepLink->getFlowStep();
            }
        }

        return $this->steps;
    }

    /**
     * @return Collection|FlowField[]
     */
    public function getFields(): Collection
    {
        if ($this->fields === null) {
            $this->fields = new ArrayCollection();
            foreach ($this->getSteps() as $step) {
                foreach ($step->getFields() as $field) {
                    $this->fields[] = $field;
                }
            }
        }

        return $this->fields;
    }

    public function getField(string $fieldId): FlowField
    {
        $found = $this->getFields()->filter(function (FlowField $field) use ($fieldId) {
            return $field->getFieldId() === $fieldId;
        });

        if ($found->count() === 0) {
            throw new NotFoundException("No field $fieldId found in flow {$this->getKey()}");
        }

        return $found->current();
    }

    public function setBaseObject(string $baseObject): void
    {
        $this->baseObject = $baseObject;
    }

    public function setIsConfig(bool $isConfig): void
    {
        $this->isConfig = $isConfig;
    }

    public function setExternal(bool $external): void
    {
        $this->external = $external;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
