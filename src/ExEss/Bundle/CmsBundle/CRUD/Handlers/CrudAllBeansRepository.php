<?php

namespace ExEss\Bundle\CmsBundle\CRUD\Handlers;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\AbstractRepository;
use ExEss\Bundle\CmsBundle\Base\Response\BaseListResponse;
use ExEss\Bundle\CmsBundle\CRUD\Handlers\Response\AllFatEntitiesResponse;
use ExEss\Bundle\CmsBundle\CRUD\Helpers\SecurityService;

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
