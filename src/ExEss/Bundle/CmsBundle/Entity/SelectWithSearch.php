<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;

/**
 * @ORM\Table(name="fe_selectwithsearch", indexes={
 *     @ORM\Index(name="fk_users_id_d9e552b0", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_7fce87b3", columns={"created_by"})
 * })
 * @ORM\Entity(repositoryClass="ExEss\Bundle\CmsBundle\Repository\SelectWithSearchRepository")
 */
class SelectWithSearch extends Entity
{
    public const NAME_CRUD_NEW_RELATION = 'crud_new_relationship';

    /**
     * @ORM\Column(name="base_object", type="string", length=255, nullable=true)
     */
    private ?string $baseObject = null;

    /**
     * @ORM\Column(name="filters", type="text", length=65535, nullable=true)
     */
    private ?string $filters = null;

    /**
     * @ORM\Column(name="items_on_page", type="integer", options={"default"="50"})
     */
    private int $itemsOnPage = 50;

    /**
     * @ORM\Column(name="order_by", type="string", length=255, nullable=true)
     */
    private ?string $orderBy = null;

    /**
     * @ORM\Column(name="option_label", type="string", length=255, nullable=true)
     */
    private ?string $optionLabel = null;

    /**
     * @ORM\Column(name="filter_string", type="string", length=255, nullable=true)
     */
    private ?string $filterString = null;

    /**
     * @ORM\Column(name="option_key", type="string", length=255, nullable=true)
     */
    private ?string $optionKey = null;

    /**
     * @ORM\Column(name="needs_query", type="boolean", nullable=false)
     */
    private bool $needsQuery;

    public function getBaseObject(): ?string
    {
        return $this->baseObject;
    }

    public function getFilters(): ?string
    {
        return $this->filters;
    }

    public function getItemsOnPage(): int
    {
        return $this->itemsOnPage;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    public function getOptionLabel(): ?string
    {
        return $this->optionLabel;
    }

    public function getFilterString(): ?string
    {
        return $this->filterString;
    }

    public function getOptionKey(): ?string
    {
        return $this->optionKey;
    }

    public function isNeedsQuery(): bool
    {
        return $this->needsQuery;
    }

    public function setBaseObject(?string $baseObject): void
    {
        $this->baseObject = $baseObject;
    }

    public function setFilters(?string $filters): void
    {
        $this->filters = $filters;
    }

    public function setItemsOnPage(?string $itemsOnPage): void
    {
        $this->itemsOnPage = $itemsOnPage;
    }

    public function setOrderBy(?string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    public function setOptionLabel(?string $optionLabel): void
    {
        $this->optionLabel = $optionLabel;
    }

    public function setFilterString(?string $filterString): void
    {
        $this->filterString = $filterString;
    }

    public function setOptionKey(?string $optionKey): void
    {
        $this->optionKey = $optionKey;
    }

    public function setNeedsQuery(bool $needsQuery): void
    {
        $this->needsQuery = $needsQuery;
    }
}
