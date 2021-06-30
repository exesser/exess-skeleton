<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;
use ExEss\Bundle\CmsBundle\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="grid_gridtemplates", indexes={
 *     @ORM\Index(name="fk_users_id_5a0588cb", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_45b3d681", columns={"created_by"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="gridTemplates",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_grid_template",
 *              joinColumns=@ORM\JoinColumn(name="grid_template_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity(repositoryClass="ExEss\Bundle\CmsBundle\Repository\GridTemplateRepository")
 */
class GridTemplate extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="key_c", type="string", length=255, nullable=true)
     */
    private ?string $key = null;

    /**
     * @ORM\Column(name="json_fields_c", type="json", length=65535, nullable=true)
     */
    private ?array $jsonFields = null;

    /**
     * @ORM\OneToMany(targetEntity="FlowStep", mappedBy="gridTemplate")
     * @var Collection|FlowStep[]
     */
    private Collection $steps;

    public function __construct()
    {
        $this->steps = new ArrayCollection();
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getJsonFields(): ?array
    {
        return $this->jsonFields;
    }

    /**
     * @return Collection|FlowStep[]
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }
}
