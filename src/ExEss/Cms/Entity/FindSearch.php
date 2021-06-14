<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="find_search", indexes={
 *     @ORM\Index(name="fk_users_id_739bb32e", columns={"created_by"}),
 *     @ORM\Index(name="fk_users_id_12ec510a", columns={"modified_user_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="findSearches",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_find_search",
 *              joinColumns=@ORM\JoinColumn(name="find_search_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class FindSearch extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="link_to", type="enum_link_to", nullable=true)
     */
    private ?string $linkTo = null;

    /**
     * @ORM\Column(name="params", type="json", nullable=true)
     */
    private ?array $params = null;

    /**
     * @ORM\OneToMany(targetEntity="Dashboard", mappedBy="search")
     * @var Collection|Dashboard[]
     */
    private Collection $dashboards;

    public function __construct()
    {
        $this->dashboards = new ArrayCollection();
    }

    public function getLinkTo(): ?string
    {
        return $this->linkTo;
    }

    public function setLinkTo(?string $linkTo): void
    {
        $this->linkTo = $linkTo;
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function setParams(?array $params): void
    {
        $this->params = $params;
    }
}
