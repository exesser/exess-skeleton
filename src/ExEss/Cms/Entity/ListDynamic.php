<?php declare(strict_types=1);

namespace ExEss\Cms\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Cms\Entity\Base\Entity;
use ExEss\Cms\Entity\Behavior\SecurityGroups;

/**
 * @ORM\Table(name="list_dynamic_list", indexes={
 *     @ORM\Index(name="fk_users_id_8ba3dc94", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_a0c59bd5", columns={"created_by"})
 * })
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(
 *          name="securityGroups",
 *          inversedBy="lists",
 *          joinTable=@ORM\JoinTable(
 *              name="security_group_list",
 *              joinColumns=@ORM\JoinColumn(name="list_id", onDelete="CASCADE")
 *          )
 *      )
 * })
 * @ORM\Entity(repositoryClass="ExEss\Cms\Repository\ListRepository")
 */
class ListDynamic extends Entity
{
    use SecurityGroups;

    /**
     * @ORM\Column(name="display_footer", type="boolean", nullable=true, options={"default"="0"})
     */
    private ?bool $displayFooter = false;

    /**
     * @ORM\Column(name="base_object", type="string", length=255, nullable=true)
     */
    private ?string $baseObject = null;

    /**
     * @ORM\Column(name="standard_filter", type="json", nullable=true)
     */
    private ?array $standardFilter = null;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private ?string $title = null;

    /**
     * @ORM\Column(name="items_per_page", type="integer", options={"default"="10"})
     */
    private int $itemsPerPage = 10;

    /**
     * @ORM\Column(name="default_filter_values", type="json", nullable=true)
     */
    private ?array $defaultFilterValues = null;

    /**
     * @ORM\Column(name="filters_have_changed", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $filtersHaveChanged = false;

    /**
     * @ORM\Column(name="combined", type="boolean", nullable=true, options={"default":"0"})
     */
    private ?bool $combined = false;

    /**
     * @ORM\Column(name="responsive", type="boolean", nullable=false, options={"default":"0"})
     */
    private bool $responsive = false;

    /**
     * @ORM\Column(name="fix_pagination", type="boolean", nullable=false, options={"default"="1"})
     */
    private bool $fixPagination = true;

    /**
     * @ORM\Column(name="quick_search", type="boolean", nullable=false, options={"default":"0"})
     */
    private bool $quickSearch = false;

    /**
     * @ORM\OneToMany(targetEntity="GridPanel", mappedBy="list")
     * @var Collection|GridPanel[]
     */
    private Collection $gridPanels;

    /**
     * @ORM\OneToMany(targetEntity="ListCellLink", mappedBy="list")
     * @ORM\OrderBy({"order" = "asc"})
     * @var Collection|ListCellLink[]
     */
    private Collection $cellLinks;

    /**
     * @ORM\OneToMany(targetEntity="ExternalObjectLink", mappedBy="list")
     * @var Collection|ExternalObjectLink[]
     */
    private Collection $linkFields;

    /**
     * @ORM\ManyToOne(targetEntity="Filter", inversedBy="lists")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Filter $filter = null;

    /**
     * @ORM\ManyToOne(targetEntity="ListTopBar", inversedBy="lists")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ListTopBar $topBar = null;

    /**
     * @ORM\ManyToOne(targetEntity="ExternalObject", inversedBy="lists")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ExternalObject $externalObject = null;

    public function __construct()
    {
        $this->cellLinks = new ArrayCollection();
        $this->gridPanels = new ArrayCollection();
    }

    /**
     * @return Collection|ListCellLink[]
     */
    public function getCellLinks(): Collection
    {
        return $this->cellLinks;
    }

    public function isDisplayFooter(): bool
    {
        return (bool) $this->displayFooter;
    }

    public function getBaseObject(): ?string
    {
        return $this->baseObject;
    }

    public function getStandardFilter(): ?array
    {
        return $this->standardFilter;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getDefaultFilterValues(): ?array
    {
        return $this->defaultFilterValues;
    }

    public function getFiltersHaveChanged(): ?bool
    {
        return $this->filtersHaveChanged;
    }

    public function isCombined(): bool
    {
        return (bool) $this->combined;
    }

    public function isResponsive(): bool
    {
        return $this->responsive;
    }

    public function isFixPagination(): bool
    {
        return $this->fixPagination;
    }

    public function isQuickSearch(): bool
    {
        return $this->quickSearch;
    }

    public function getFilter(): ?Filter
    {
        return $this->filter;
    }

    public function getTopBar(): ?ListTopBar
    {
        return $this->topBar;
    }

    public function getExternalObject(): ?ExternalObject
    {
        return $this->externalObject;
    }

    /**
     * @return ArrayCollection|FilterField[]
     */
    public function getFilterFields(): ArrayCollection
    {
        $filterFields = new ArrayCollection();
        if ($filter = $this->getFilter()) {
            foreach ($filter->getGroups() as $filterGroup) {
                foreach ($filterGroup->getFields() as $field) {
                    $filterFields[] = $field;
                }
            }
        }

        return $filterFields;
    }

    public function setFiltersHaveChanged(bool $filtersHaveChanged): void
    {
        $this->filtersHaveChanged = $filtersHaveChanged;
    }

    public function isExternal(): bool
    {
        return $this->getExternalObject() !== null;
    }
}
