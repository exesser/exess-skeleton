<?php

namespace ExEss\Cms\Base\Response;

class Pagination implements \JsonSerializable
{
    private int $page;

    private int $limit;

    private int $total;

    private ?int $totalPages;

    public function __construct(
        int $page,
        int $limit,
        int $total,
        ?int $totalPages = null
    ) {
        $this->page = $page;
        $this->limit = $limit;
        $this->total = $total;
        $this->totalPages = $totalPages;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function jsonSerialize(): array
    {
        return [
            'page' => $this->page,
            'limit' => $this->limit,
            'total' => $this->total,
            'totalPages' => $this->totalPages,
        ];
    }
}
