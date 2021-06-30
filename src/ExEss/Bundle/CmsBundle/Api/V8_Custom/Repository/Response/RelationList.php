<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\Response;

use ExEss\Bundle\CmsBundle\Base\Response\BaseListResponse;

class RelationList extends BaseListResponse
{
    /**
     * @var array|RelationRow[]
     */
    private array $relations = [];

    /**
     * @inheritdoc
     *
     * @return RelationRow[]
     */
    public function getList(): iterable
    {
        return $this->relations;
    }

    public function addRelation(RelationRow $relation): void
    {
        $this->relations[] = $relation;
    }

    /**
     * @param array|RelationRow[] $relations
     */
    public function setRelations(array $relations): void
    {
        $this->relations = $relations;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return $this->getList();
    }
}
