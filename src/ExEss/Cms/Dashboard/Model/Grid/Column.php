<?php
namespace ExEss\Cms\Dashboard\Model\Grid;

use ExEss\Cms\Dashboard\Model\StripEmptyOnEncodeTrait;

class Column implements \JsonSerializable
{
    use StripEmptyOnEncodeTrait;

    private ?string $size = null;

    private ?bool $hasMargin = null;

    /**
     * @var Row[]
     */
    private array $rows = [];

    private array $cssClasses = [];

    /**
     * @throws \InvalidArgumentException In case the argument contains unsupported options.
     */
    public function __construct(array $source)
    {
        if (($rows = $source['rows'] ?? false) && \is_array($rows)) {
            foreach ($rows as $row) {
                $this->addRow(new Row($row), \count($this->rows));
            }
            unset($source['rows']);
        }
        if (($size = $source['size'] ?? false) && \is_string($size)) {
            $this->setSize($size);
            unset($source['size']);
        }
        $hasMargin = $source['hasMargin'] ?? null;
        if ($hasMargin !== null) {
            $this->setHasMargin($hasMargin);
            unset($source['hasMargin']);
        }
        if (($cssClasses = $source['cssClasses'] ?? false) !== false) {
            $this->setCssClasses($cssClasses);
            unset($source['cssClasses']);
        }

        if (\count($source)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unsupported column options: %s',
                \implode(', ', \array_keys($source))
            ));
        }
    }

    public function setSize(string $size): Column
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function addRow(Row $row, int $key): Column
    {
        \reset($this->rows);
        \array_splice($this->rows, $key, 0, [$row]);

        return $this;
    }

    public function removeRow(Row $row): Column
    {
        $this->rows = \array_filter(
            $this->rows,
            function ($value) use ($row) {
                return $value !== $row;
            }
        );

        return $this;
    }

    /**
     * @return Row[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    public function setHasMargin(bool $hasMargin): Column
    {
        $this->hasMargin = $hasMargin;

        return $this;
    }

    public function isHasMargin(): ?bool
    {
        return $this->hasMargin;
    }

    public function setCssClasses(array $cssClasses): Column
    {
        $this->cssClasses = $cssClasses;

        return $this;
    }

    public function getCssClasses(): array
    {
        return $this->cssClasses;
    }
}
