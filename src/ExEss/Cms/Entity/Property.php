<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="properties", indexes={
 *     @ORM\Index(name="fk_users_id_0a713cf0", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_96f8b053", columns={"created_by"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="properties",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_property",
 *              joinColumns=@ORM\JoinColumn(name="property_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class Property extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="value_c", type="string", length=255, nullable=true)
     */
    private ?string $value = null;

    /**
     * @ORM\ManyToMany(targetEntity="FlowStep", mappedBy="properties")
     * @var Collection|FlowStep[]
     */
    private Collection $steps;

    /**
     * @ORM\ManyToMany(targetEntity="Dashboard", mappedBy="properties")
     * @var Collection|Dashboard[]
     */
    private Collection $dashboards;

    public function __construct()
    {
        $this->steps = new ArrayCollection();
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }
}
