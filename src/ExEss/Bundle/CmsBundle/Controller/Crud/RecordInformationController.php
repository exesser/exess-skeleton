<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\Crud;

use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
use ExEss\Bundle\CmsBundle\Service\CrudService;
use Symfony\Component\Routing\Annotation\Route;

class RecordInformationController
{
    private CrudService $crudService;

    public function __construct(CrudService $crudService)
    {
        $this->crudService = $crudService;
    }

    /**
     * @Route("/crud/record/information", methods={"GET"})
     */
    public function __invoke(): SuccessResponse
    {
        return new SuccessResponse($this->crudService->getRecordsInformation());
    }
}
