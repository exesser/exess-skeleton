<?php
namespace ExEss\Bundle\CmsBundle\Dashboard\Model;

use ExEss\Bundle\CmsBundle\Helper\DataCleaner;

class Grid implements \JsonSerializable
{
    use StripEmptyOnEncodeTrait;

    private ?string $size = null;

    /**
     * @var Grid\Column[]
     */
    private array $columns = [];

    private array $cssClasses = [];

    /**
     * @param array|string $source
     *
     * @throws \InvalidArgumentException In case the argument is invalid json or contains unsupported options.
     */
    public function __construct($source)
    {
        if (\is_string($source)) {
            $source = DataCleaner::jsonDecode($source);
        }

        if (!\is_array($source)) {
            throw new \InvalidArgumentException(\sprintf(
                'argument must be valid json string or array, %s given',
                \gettype($source)
            ));
        }

        if (($columns = $source['columns'] ?? false) && \is_array($columns)) {
            foreach ($columns as $column) {
                $this->addColumn(new Grid\Column($column));
            }
            unset($source['columns']);
        }
        if (($cssClasses = $source['cssClasses'] ?? false) !== false) {
            $this->setCssClasses($cssClasses);
            unset($source['cssClasses']);
        }
        if (($size = $source['size'] ?? false) !== false) {
            $this->setSize($size);
            unset($source['size']);
        }

        if (\count($source)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unsupported grid options: %s',
                \implode(', ', \array_keys($source))
            ));
        }
    }

    public function setSize(string $size): Grid
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function addColumn(Grid\Column $column): Grid
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * @return Grid\Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function setCssClasses(array $cssClasses): Grid
    {
        $this->cssClasses = $cssClasses;

        return $this;
    }

    public function getCssClasses(): array
    {
        return $this->cssClasses;
    }
}
