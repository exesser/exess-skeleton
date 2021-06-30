<?php
namespace ExEss\Bundle\CmsBundle\Dashboard\Model\Grid;

use ExEss\Bundle\CmsBundle\Dashboard\Model\Grid;
use ExEss\Bundle\CmsBundle\Dashboard\Model\StripEmptyOnEncodeTrait;

class Row implements \JsonSerializable
{
    use StripEmptyOnEncodeTrait;

    public const TYPE_EMBEDDED_GUIDANCE = 'embeddedGuidance';

    private ?string $size = null;

    private ?bool $hasMargin = null;

    private ?string $type = null;

    private ?string $panelKey = null;

    private Row\Options $options;

    private array $cssClasses = [];

    private array $children = [];

    private ?Grid $grid = null;

    /**
     * @throws \InvalidArgumentException In case the argument contains unsupported options.
     */
    public function __construct(array $source)
    {
        $options = $source['options'] ?? [];
        $this->setOptions(new Row\Options($options));
        unset($source['options']);

        if (($children = $source['children'] ?? false) !== false && \is_array($children)) {
            foreach ($children as $child) {
                $this->addChild(new Row($child));
            }
            unset($source['children']);
        }
        if (($size = $source['size'] ?? false) !== false) {
            $this->setSize($size);
            unset($source['size']);
        }
        if (($panelKey = $source['panelKey'] ?? false) !== false) {
            $this->setPanelKey($panelKey);
            unset($source['panelKey']);
        }
        if (($hasMargin = $source['hasMargin'] ?? null) !== null) {
            $this->setHasMargin($hasMargin);
            unset($source['hasMargin']);
        }
        if (($type = $source['type'] ?? false) && \is_string($type)) {
            $this->setType($type);
            unset($source['type']);
        }
        if (($cssClasses = $source['cssClasses'] ?? false) !== false) {
            $this->setCssClasses($cssClasses);
            unset($source['cssClasses']);
        }
        if (($grid = $source['grid'] ?? false) !== false && \is_array($grid)) {
            $this->setGrid(new Grid($grid));
            unset($source['grid']);
        }

        if (\count($source)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unsupported row options: %s',
                \implode(', ', \array_keys($source))
            ));
        }
    }

    public function setSize(string $size): Row
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setHasMargin(bool $hasMargin): Row
    {
        $this->hasMargin = $hasMargin;

        return $this;
    }

    public function isHasMargin(): ?bool
    {
        return $this->hasMargin;
    }

    public function setType(string $type): Row
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setPanelKey(string $panelKey): Row
    {
        $this->panelKey = $panelKey;

        return $this;
    }

    public function getPanelKey(): ?string
    {
        return $this->panelKey;
    }

    public function setCssClasses(array $cssClasses): Row
    {
        $this->cssClasses = $cssClasses;

        return $this;
    }

    public function getCssClasses(): array
    {
        return $this->cssClasses;
    }

    public function setOptions(Row\Options $options): Row
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): Row\Options
    {
        return $this->options;
    }

    public function addChild(Row $row): Row
    {
        $this->children[] = $row;

        return $this;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function setGrid(Grid $grid): Row
    {
        $this->grid = $grid;

        return $this;
    }

    public function getGrid(): ?Grid
    {
        return $this->grid;
    }
}
