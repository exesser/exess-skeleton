<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="fltrs_fields", indexes={
 *     @ORM\Index(name="fk_users_id_d334c340", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_82b17b3c", columns={"created_by"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="filterFields",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_filter_field",
 *              joinColumns=@ORM\JoinColumn(name="filter_field_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class FilterField extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="label_c", type="string", length=255, nullable=true)
     */
    private ?string $label = null;

    /**
     * @ORM\Column(name="operator", type="string", length=6, nullable=true, options={"default"="="})
     */
    private ?string $operator = '=';

    /**
     * @ORM\Column(name="field_key_c", type="string", length=255, nullable=true)
     */
    private ?string $fieldKey = null;

    /**
     * @ORM\Column(name="field_sql_c", type="text", length=65535, nullable=true)
     */
    private ?string $fieldSql = null;

    /**
     * @ORM\Column(name="field_options_c", type="json", nullable=true)
     */
    private ?array $fieldOptions = null;

    /**
     * @ORM\Column(name="field_options_enum_key_c", type="string", length=255, nullable=true)
     */
    private ?string $fieldOptionsEnumKey = null;

    /**
     * @ORM\Column(name="sort_c", type="string", length=4, nullable=true, options={"default"="10"})
     */
    private ?string $sort = '10';

    /**
     * @ORM\Column(name="type_c", type="enum_filter_field_type", nullable=true)
     */
    private ?string $type = null;

    /**
     * @ORM\ManyToMany(targetEntity="FilterFieldGroup", mappedBy="fields")
     * @var Collection|FilterFieldGroup[]
     */
    private Collection $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getOperator(): ?string
    {
        return $this->operator;
    }

    public function setOperator(?string $operator): self
    {
        $this->operator = $operator;

        return $this;
    }

    public function getFieldKey(): ?string
    {
        return $this->fieldKey;
    }

    public function setFieldKey(?string $fieldKey): self
    {
        $this->fieldKey = $fieldKey;

        return $this;
    }

    public function getFieldSql(): ?string
    {
        return $this->fieldSql;
    }

    public function setFieldSql(?string $fieldSql): self
    {
        $this->fieldSql = $fieldSql;

        return $this;
    }

    public function getFieldOptions(): ?array
    {
        return $this->fieldOptions;
    }

    public function setFieldOptions(?string $fieldOptions): self
    {
        $this->fieldOptions = $fieldOptions;

        return $this;
    }

    public function getFieldOptionsEnumKey(): ?string
    {
        return $this->fieldOptionsEnumKey;
    }

    public function setFieldOptionsEnumKey(?string $fieldOptionsEnumKey): self
    {
        $this->fieldOptionsEnumKey = $fieldOptionsEnumKey;

        return $this;
    }

    public function getSort(): ?string
    {
        return $this->sort;
    }

    public function setSort(?string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|FilterFieldGroup[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }
}
