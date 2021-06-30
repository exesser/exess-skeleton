<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;
use ExEss\Bundle\CmsBundle\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="list_external_object_linkfields", indexes={
 *     @ORM\Index(name="fk_users_id_c8fd1c5c", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_1356a3c9", columns={"created_by"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="externalObjectLinks",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_external_object_link",
 *              joinColumns=@ORM\JoinColumn(name="external_object_link_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class ExternalObjectLink extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="entity_name", type="string", length=255, nullable=true)
     */
    private ?string $entityName = null;

    /**
     * @ORM\Column(name="entity_field", type="string", length=255, nullable=true)
     */
    private ?string $entityField = null;

    /**
     * @ORM\ManyToOne(targetEntity="ListDynamic", inversedBy="linkFields")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ListDynamic $list = null;

    /**
     * @ORM\ManyToOne(targetEntity="ExternalObject", inversedBy="linkFields")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ExternalObject $externalObject = null;

    public function getEntityName(): ?string
    {
        return $this->entityName;
    }

    public function getEntityField(): ?string
    {
        return $this->entityField;
    }

    public function getList(): ?ListDynamic
    {
        return $this->list;
    }
}
