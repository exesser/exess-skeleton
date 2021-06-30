<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ExEss\Bundle\CmsBundle\Doctrine\Type\CellVisibility;
use ExEss\Bundle\CmsBundle\Entity\Base\Entity;

/**
 * @ORM\Table(name="list_cell", indexes={
 *     @ORM\Index(name="fk_users_id_0412f82d", columns={"modified_user_id"}),
 *     @ORM\Index(name="fk_users_id_63c8a025", columns={"created_by"})
 * })
 * @ORM\Entity
 */
class ListCell extends Entity
{
    /**
     * @ORM\Column(name="type", type="enum_cell_type", nullable=true)
     */
    private ?string $type = null;

    /**
     * @ORM\Column(name="line1", type="string", length=4000, nullable=true)
     */
    private ?string $line1 = null;

    /**
     * @ORM\Column(name="line2", type="string", length=4000, nullable=true)
     */
    private ?string $line2 = null;

    /**
     * @ORM\Column(name="line3", type="string", length=4000, nullable=true)
     */
    private ?string $line3 = null;

    /**
     * @ORM\Column(name="action_key", type="string", length=255, nullable=true)
     */
    private ?string $actionKey = null;

    /**
     * @ORM\Column(name="column_label", type="string", length=255, nullable=true)
     */
    private ?string $columnLabel = null;

    /**
     * @ORM\Column(name="linkto", type="enum_link_to", nullable=true)
     */
    private ?string $linkto = null;

    /**
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    private ?string $link = null;

    /**
     * @ORM\Column(name="params_c", type="json", nullable=true)
     */
    private ?array $params = null;

    /**
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     */
    private ?string $icon = null;

    /**
     * @ORM\Column(name="line1csvheader", type="string", length=255, nullable=true)
     */
    private ?string $line1CsvHeader = null;

    /**
     * @ORM\Column(name="line2csvheader", type="string", length=255, nullable=true)
     */
    private ?string $line2CsvHeader = null;

    /**
     * @ORM\Column(name="line3csvheader", type="string", length=255, nullable=true)
     */
    private ?string $line3CsvHeader = null;

    /**
     * @ORM\Column(name="visible_c", type="enum_cell_visibility", nullable=true, options={"default"="DEFAULT"})
     */
    private ?string $visible = CellVisibility::DEFAULT;

    /**
     * @ORM\OneToMany(targetEntity="ListCellLink", mappedBy="cell")
     * @var Collection|ListCellLink[]
     */
    private Collection $cellLinks;

    public function __construct()
    {
        $this->cellLinks = new ArrayCollection();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getLine1(): ?string
    {
        return $this->line1;
    }

    public function getLine2(): ?string
    {
        return $this->line2;
    }

    public function getLine3(): ?string
    {
        return $this->line3;
    }

    public function getActionKey(): ?string
    {
        return $this->actionKey;
    }

    public function getColumnLabel(): ?string
    {
        return $this->columnLabel;
    }

    public function getLinkto(): ?string
    {
        return $this->linkto;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getLine1CsvHeader(): ?string
    {
        return $this->line1CsvHeader;
    }

    public function getLine2CsvHeader(): ?string
    {
        return $this->line2CsvHeader;
    }

    public function getLine3CsvHeader(): ?string
    {
        return $this->line3CsvHeader;
    }

    public function getCellLinks(): Collection
    {
        return $this->cellLinks;
    }

    public function isVisible(bool $exportToCSV): bool
    {
        return
            ($exportToCSV && $this->visible !== CellVisibility::IN_DWP)
            || (!$exportToCSV && $this->visible !== CellVisibility::IN_CSV);
    }
}
