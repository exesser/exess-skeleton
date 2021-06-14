<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Component\Doctrine\Mapping\Annotation\Auditable;
use ExEss\Cms\Doctrine\Type\GridType;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="grid_panels", indexes={
 *     @ORM\Index(name="fk_users_id_83e8c295", columns={"created_by"}),
 *     @ORM\Index(name="fk_users_id_7d732f01", columns={"modified_user_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="gridPanels",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_grid_panel",
 *              joinColumns=@ORM\JoinColumn(name="grid_panel_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity(repositoryClass="ExEss\Cms\Repository\GridPanelRepository")
 */
class GridPanel extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="key_c", type="string", length=255, nullable=true)
     */
    private ?string $key = null;

    /**
     * @ORM\Column(name="type", type="enum_grid_type", nullable=true, options={"default"="list"})
     */
    private ?string $type = GridType::LIST;

    /**
     * @ORM\Column(name="params", type="json", nullable=true)
     */
    private ?array $params = null;

    /**
     * @ORM\Column(name="record_type", type="string", length=255, nullable=true)
     */
    private ?string $recordType = null;

    /**
     * @ORM\Column(name="flow_id", type="string", length=255, nullable=true)
     */
    private ?string $flowId = null;

    /**
     * @ORM\Column(name="flow_action", type="enum_flow_action", nullable=true)
     */
    private ?string $flowAction = null;

    /**
     * @ORM\Column(name="record_id", type="string", length=255, nullable=true)
     */
    private ?string $recordId = null;

    /**
     * @ORM\Column(name="show_primary_button", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $showPrimaryButton = false;

    /**
     * @ORM\Column(name="primary_button_title", type="string", length=255, nullable=true)
     */
    private ?string $primaryButtonTitle = null;

    /**
     * @ORM\Column(name="default_title", type="string", length=255, nullable=true)
     */
    private ?string $defaultTitle = null;

    /**
     * @ORM\Column(name="title_expression", type="string", length=255, nullable=true)
     */
    private ?string $titleExpression = null;

    /**
     * @ORM\Column(name="size", type="string", length=255, nullable=true)
     */
    private ?string $size = null;

    /**
     * @ORM\Column(name="list_key", type="string", length=255, nullable=true)
     */
    private ?string $listKey = null;

    /**
     * @ORM\ManyToMany(targetEntity="Validator", inversedBy="panels")
     * @ORM\JoinTable(name="grid_panels_flw_guidancefieldvalidators_1_c")
     * @ORM\OrderBy({"validationGroup" = "asc"})
     * @Auditable
     * @var Collection|Validator[]
     */
    private Collection $conditions;

    public function __construct()
    {
        $this->conditions = new ArrayCollection();
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function getRecordType(): ?string
    {
        return $this->recordType;
    }

    public function getFlowId(): ?string
    {
        return $this->flowId;
    }

    public function getFlowAction(): ?string
    {
        return $this->flowAction;
    }

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getShowPrimaryButton(): ?bool
    {
        return $this->showPrimaryButton;
    }

    public function getPrimaryButtonTitle(): ?string
    {
        return $this->primaryButtonTitle;
    }

    public function getDefaultTitle(): ?string
    {
        return $this->defaultTitle;
    }

    public function getTitleExpression(): ?string
    {
        return $this->titleExpression;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function getListKey(): ?string
    {
        return $this->listKey;
    }

    /**
     * @return Collection|Validator[]
     */
    public function getConditions(): Collection
    {
        return $this->conditions;
    }
}
