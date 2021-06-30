<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\DoctrineExtensionsBundle\Mapping\Annotation\Auditable;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;
use ExEss\Bundle\CmsBundle\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="list_topbar", indexes={
 *     @ORM\Index(name="fk_users_id_2a9c7a03", columns={"created_by"}),
 *     @ORM\Index(name="fk_users_id_c19ecc09", columns={"modified_user_id"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="listTopBars",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_list_top_bar",
 *              joinColumns=@ORM\JoinColumn(name="list_top_bar_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity
 */
class ListTopBar extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="selectall", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $selectAll = false;

    /**
     * @ORM\Column(name="can_export_to_csv_c", type="boolean", nullable=true)
     */
    private ?bool $canExportToCsv = null;

    /**
     * @ORM\ManyToMany(targetEntity="ListSortingOption", inversedBy="topBars")
     * @ORM\JoinTable(name="list_topbar_list_sorting_options_c")
     * @Auditable
     * @var Collection|ListSortingOption[]
     */
    private Collection $sortingOptions;

    /**
     * @ORM\ManyToMany(targetEntity="ListTopAction", inversedBy="topBars")
     * @ORM\JoinTable(name="list_topbar_list_top_action_c")
     * @Auditable
     * @var Collection|ListTopAction[]
     */
    private Collection $actions;

    /**
     * @ORM\OneToMany(targetEntity="ListDynamic", mappedBy="topBar")
     * @var Collection|ListDynamic[]
     */
    private Collection $lists;

    public function __construct()
    {
        $this->sortingOptions = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->lists = new ArrayCollection();
    }

    public function getSelectAll(): bool
    {
        return (bool) $this->selectAll;
    }

    public function getCanExportToCsv(): bool
    {
        return (bool) $this->canExportToCsv;
    }

    /**
     * @return Collection|ListSortingOption[]
     */
    public function getSortingOptions(): Collection
    {
        return $this->sortingOptions;
    }

    /**
     * @return Collection|ListTopAction[]
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    /**
     * @return Collection|ListDynamic[]
     */
    public function getLists(): Collection
    {
        return $this->lists;
    }

    public function setSelectAll(?bool $selectAll): void
    {
        $this->selectAll = $selectAll;
    }

    public function setCanExportToCsv(?bool $canExportToCsv): void
    {
        $this->canExportToCsv = $canExportToCsv;
    }

    /**
     * @param Collection|ListSortingOption[] $sortingOptions
     */
    public function setSortingOptions(Collection $sortingOptions): void
    {
        $this->sortingOptions = $sortingOptions;
    }

    /**
     * @param Collection|ListTopAction[] $actions
     */
    public function setActions(Collection $actions): void
    {
        $this->actions = $actions;
    }

    /**
     * @param Collection|ListDynamic[] $lists
     */
    public function setLists(Collection $lists): void
    {
        $this->lists = $lists;
    }
}
