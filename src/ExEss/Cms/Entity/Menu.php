<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Component\Doctrine\Mapping\Annotation\Auditable;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="menu_mainmenu", indexes={
 *     @ORM\Index(name="fk_users_id_2a84509c", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_05843a67", columns={"created_by"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="menus",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_menu",
 *              joinColumns=@ORM\JoinColumn(name="menu_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity(repositoryClass="ExEss\Cms\Repository\MenuRepository")
 */
class Menu extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="params_c", type="json", nullable=true)
     */
    private ?array $params = null;

    /**
     * @ORM\Column(name="display_order_c", type="string", length=255, nullable=true, options={"default"="10"})
     */
    private ?string $displayOrder = '10';

    /**
     * @ORM\Column(name="link_c", type="string", length=255, nullable=true)
     */
    private ?string $link = null;

    /**
     * @ORM\Column(name="icon_c", type="string", length=255, nullable=true)
     */
    private ?string $icon = null;

    /**
     * @ORM\ManyToMany(targetEntity="Dashboard", inversedBy="menus")
     * @ORM\JoinTable(name="menu_mainmenu_dash_dashboard_c")
     * @Auditable
     * @var Collection|Dashboard[]
     */
    private Collection $dashboards;

    public function __construct()
    {
        $this->dashboards = new ArrayCollection();
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function getDisplayOrder(): ?string
    {
        return $this->displayOrder;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @return Collection|Dashboard[]
     */
    public function getDashboards(): Collection
    {
        return $this->dashboards;
    }
}
