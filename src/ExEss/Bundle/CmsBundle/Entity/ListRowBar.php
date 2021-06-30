<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;

/**
 * @ORM\Table(name="list_row_bar", indexes={
 *     @ORM\Index(name="fk_users_id_31ec3949", columns={"created_by"}),
 *     @ORM\Index(name="fk_users_id_8fd6281b", columns={"modified_user_id"})
 * })
 * @ORM\Entity
 */
class ListRowBar extends Entity
{
    /**
     * @ORM\OneToMany(targetEntity="ListCellLink", mappedBy="rowBar")
     * @var Collection|ListCellLink[]
     */
    private Collection $cellLinks;

    /**
     * @ORM\OneToMany(targetEntity="ListRowAction", mappedBy="rowBar")
     * @var Collection|ListRowAction[]
     */
    private Collection $rowActions;

    public function __construct()
    {
        $this->cellLinks = new ArrayCollection();
        $this->rowActions = new ArrayCollection();
    }

    /**
     * @return Collection|ListCellLink[]
     */
    public function getCellLinks(): Collection
    {
        return $this->cellLinks;
    }

    /**
     * @return Collection|ListRowAction[]
     */
    public function getRowActions(): Collection
    {
        return $this->rowActions;
    }
}
