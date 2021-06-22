<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\DoctrineExtensionsBundle\Mapping\Annotation\Auditable;
use ExEss\Cms\Doctrine\Type\FlowStepType;
use ExEss\Cms\Entity\Base\Entity;

/**
 * @ORM\Table(name="flw_flowsteps", indexes={
 *     @ORM\Index(name="fk_users_id_173ce7ba", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_f1bb7a06", columns={"created_by"})
 * })
 * @ORM\Entity
 */
class FlowStep extends Entity
{
    /**
     * @ORM\Column(name="type_c", type="enum_flow_step_type", nullable=true, options={"default"="DEFAULT"})
     */
    private ?string $type = FlowStepType::DEFAULT;

    /**
     * @ORM\Column(name="json_fields_c", type="json", nullable=true)
     */
    private ?array $jsonFields = null;

    /**
     * @ORM\Column(name="key_c", type="string", length=150, nullable=true)
     */
    private ?string $key = null;

    /**
     * @ORM\Column(name="is_card_c", type="boolean", nullable=true)
     */
    private ?bool $isCard = null;

    /**
     * @ORM\Column(name="label_c", type="string", length=150, nullable=true)
     */
    private ?string $label = null;

    /**
     * @ORM\ManyToMany(targetEntity="FlowField", mappedBy="steps")
     * @ORM\OrderBy({"fieldGroup" = "asc", "order" = "asc"})
     * @var Collection|FlowField[]
     */
    private Collection $fields;

    /**
     * @ORM\ManyToMany(targetEntity="Property", inversedBy="steps")
     * @ORM\JoinTable(name="flw_flowsteps_flw_flowstepproperties_1_c")
     * @Auditable
     * @var Collection|Property[]
     */
    private Collection $properties;

    /**
     * @ORM\OneToMany(targetEntity="FlowStepLink", mappedBy="flowStep")
     * @var Collection|FlowStepLink[]
     */
    private Collection $stepLinks;

    /**
     * @ORM\ManyToOne(targetEntity="GridTemplate", inversedBy="steps")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?GridTemplate $gridTemplate = null;

    public function __construct()
    {
        $this->properties = new ArrayCollection();
        $this->fields = new ArrayCollection();
        $this->stepLinks = new ArrayCollection();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getJsonFields(): ?array
    {
        return $this->jsonFields;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getIsCard(): ?bool
    {
        return $this->isCard;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @return Collection|FlowField[]
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    /**
     * @return Collection|Property[]
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    /**
     * @return Collection|FlowStepLink[]
     */
    public function getStepLinks(): Collection
    {
        return $this->stepLinks;
    }

    public function getGridTemplate(): ?GridTemplate
    {
        return $this->gridTemplate;
    }

    public function setGridTemplate(?GridTemplate $gridTemplate): void
    {
        $this->gridTemplate = $gridTemplate;
    }
}
