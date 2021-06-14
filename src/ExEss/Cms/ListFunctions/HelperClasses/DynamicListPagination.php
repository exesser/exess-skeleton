<?php

namespace ExEss\Cms\ListFunctions\HelperClasses;

class DynamicListPagination implements \JsonSerializable
{
    public ?int $page = null;

    public ?int $size = null;

    public ?string $sortBy = null;

    public ?int $pages = null;

    public ?int $total = null;

    private bool $fixPagination = true;

    private int $currentPageSize = 0;

    public function setFixPagination(bool $fixPagination): void
    {
        $this->fixPagination = $fixPagination;
    }

    public function isFixPagination(): bool
    {
        return $this->fixPagination;
    }

    public function getPages(): int
    {
        if ($this->fixPagination) {
            return (int) \ceil($this->total / $this->size);
        }

        if ($this->currentPageSize < $this->size) {
            return $this->page;
        }

        return $this->page + 1;
    }

    public function setCurrentPageSize(int $currentPageSize): void
    {
        $this->currentPageSize = $currentPageSize;
    }

    private function getTotal(): ?int
    {
        if (!$this->fixPagination) {
            if ($this->getPages() === $this->page) {
                return (($this->getPages() - 1) * $this->size) + $this->currentPageSize;
            }

            return null;
        }

        return $this->total;
    }

    public function jsonSerialize(): array
    {
        return [
            'page' => $this->page,
            'size' => $this->size,
            'sortBy' => $this->sortBy,
            'total' => $this->getTotal(),
            'pages' => $this->getPages(),
        ];
    }
}
