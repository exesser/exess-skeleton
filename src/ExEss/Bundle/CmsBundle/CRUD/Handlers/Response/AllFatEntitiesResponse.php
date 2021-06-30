<?php

namespace ExEss\Bundle\CmsBundle\CRUD\Handlers\Response;

use ExEss\Bundle\CmsBundle\Base\Response\BaseListResponse;

class AllFatEntitiesResponse extends BaseListResponse
{
    private array $allBeans = [];

    public function __construct(array $mainBeans, array $fatEntities)
    {
        foreach (\array_chunk($mainBeans, 3) as $recordTypes) {
            $this->allBeans[] = new AllFatEntity(
                $recordTypes[0],
                $recordTypes[1] ?? '',
                $recordTypes[2] ?? ''
            );
        }

        $this->allBeans[] = new AllFatEntity('', '', '');

        foreach (\array_chunk($fatEntities, 3) as $recordTypes) {
            $this->allBeans[] = new AllFatEntity(
                $recordTypes[0],
                $recordTypes[1] ?? '',
                $recordTypes[2] ?? ''
            );
        }
    }

    public function getList(): iterable
    {
        return $this->allBeans;
    }

    public function jsonSerialize(): array
    {
        return $this->getList();
    }
}
