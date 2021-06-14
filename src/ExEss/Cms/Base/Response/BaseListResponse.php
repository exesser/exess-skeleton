<?php

namespace ExEss\Cms\Base\Response;

abstract class BaseListResponse extends BaseResponse implements \Countable, \JsonSerializable
{
    private ?Pagination $pagination = null;

    public function getPagination(): Pagination
    {
        if ($this->pagination === null) {
            $limit = $this->count();
            $this->pagination = new Pagination(1, $limit, $limit, 1);
        }

        return $this->pagination;
    }

    public function setPagination(Pagination $pagination): void
    {
        $this->pagination = $pagination;
    }

    /**
     * Return the defined list
     */
    abstract public function getList(): iterable;

    /**
     * @inheritdoc
     */
    abstract public function jsonSerialize();

    public function getResponse(): array
    {
        return \array_merge(
            [
                'list' => $this->jsonSerialize()
            ],
            $this->getPagination()->jsonSerialize()
        );
    }

    public function count(): int
    {
        return \count($this->getList());
    }
}
