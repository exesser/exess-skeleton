<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;
use ExEss\Bundle\CmsBundle\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="list_cells", indexes={
 *     @ORM\Index(name="fk_users_id_c0b56d3a", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_cbcb290a", columns={"created_by"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="listCellLinks",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_list_cell_link",
 *              joinColumns=@ORM\JoinColumn(name="list_cell_link_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class ListCellLink extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="order_c", type="integer", nullable=true, options={"default"="10"})
     */
    private ?int $order = 10;

    /**
     * @ORM\ManyToOne(targetEntity="ListRowBar", inversedBy="cellLinks")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ListRowBar $rowBar = null;

    /**
     * @ORM\ManyToOne(targetEntity="ListDynamic", inversedBy="cellLinks")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ListDynamic $list = null;

    /**
     * @ORM\ManyToOne(targetEntity="ListCell", inversedBy="cellLinks")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ListCell $cell = null;

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function getRowBar(): ?ListRowBar
    {
        return $this->rowBar;
    }

    public function getList(): ?ListDynamic
    {
        return $this->list;
    }

    public function getCell(): ?ListCell
    {
        return $this->cell;
    }
}
