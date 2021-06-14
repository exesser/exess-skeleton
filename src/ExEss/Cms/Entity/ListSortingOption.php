<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query\Expr\OrderBy;
use ExEss\Cms\Doctrine\Type\Order;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="list_sorting_options", indexes={
 *     @ORM\Index(name="fk_users_id_34c99210", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_50a5c25e", columns={"created_by"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="listSortingOptions",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_list_sorting_option",
 *              joinColumns=@ORM\JoinColumn(name="list_sorting_option_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class ListSortingOption extends Entity
{
    use SecurityGroups;

    public const DEFAULT_SORT = 'base.dateEntered';
    public const DEFAULT_ORDER = Order::DESC;

    /**
     * @ORM\Column(name="sort_key", type="string", length=255, nullable=true)
     */
    private ?string $sortKey = null;

    /**
     * @ORM\Column(name="order_by", type="enum_order", nullable=true, options={"default"="ASC"})
     */
    private ?string $orderBy = Order::ASC;

    /**
     * @ORM\ManyToMany(targetEntity="ListTopBar", mappedBy="sortingOptions")
     * @var Collection|ListTopBar[]
     */
    private Collection $topBars;

    public function __construct()
    {
        $this->topBars = new ArrayCollection();
    }

    public static function getDefault(): OrderBy
    {
        return new OrderBy(ListSortingOption::DEFAULT_SORT, ListSortingOption::DEFAULT_ORDER);
    }

    public function getSortKey(): ?string
    {
        return $this->sortKey;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    public function getTopBars(): Collection
    {
        return $this->topBars;
    }

    public function setSortKey(string $sortKey): void
    {
        $this->sortKey = $sortKey;
    }

    public function setOrderBy(string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }
}
