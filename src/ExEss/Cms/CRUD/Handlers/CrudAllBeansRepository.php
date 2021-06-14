<?php

namespace ExEss\Cms\CRUD\Handlers;

use ExEss\Cms\Api\V8_Custom\Repository\AbstractRepository;
use ExEss\Cms\Base\Response\BaseListResponse;
use ExEss\Cms\CRUD\Handlers\Response\AllFatEntitiesResponse;
use ExEss\Cms\CRUD\Helpers\SecurityService;

class CrudAllBeansRepository extends AbstractRepository
{
    private SecurityService $crudSecurity;

    public function __construct(SecurityService $crudSecurity)
    {
        $this->crudSecurity = $crudSecurity;
    }

    public function findBy(array $requestData): BaseListResponse
    {
        return $this->getRequest($requestData);
    }

    public function getRequest(array $requestData): AllFatEntitiesResponse
    {
        return new AllFatEntitiesResponse(
            $this->crudSecurity->getViewMainModules(),
            $this->crudSecurity->getViewModules()
        );
    }
}
