<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Search;

use ExEss\Bundle\CmsBundle\Base\Response\BaseListResponse;

class SearchList extends BaseListResponse
{
    /**
     * @var object[]
     */
    private array $objects = [];

    /**
     * @return object[]
     */
    public function getObjects(): array
    {
        return $this->objects;
    }

    /**
     * @param object[] $objects
     *
     */
    public function setObjects(array $objects): void
    {
        $this->objects = $objects;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->getObjects();
    }

    /**
     * @inheritdoc
     */
    public function getList(): iterable
    {
        return $this->objects;
    }
}
