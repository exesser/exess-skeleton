<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Crud;

use ExEss\Cms\Http\SuccessResponse;
use ExEss\Cms\Service\CrudService;
use Symfony\Component\Routing\Annotation\Route;

class RecordInformationController
{
    private CrudService $crudService;

    public function __construct(CrudService $crudService)
    {
        $this->crudService = $crudService;
    }

    /**
     * @Route("/Api/crud/record/information", methods={"GET"})
     */
    public function __invoke(): SuccessResponse
    {
        return new SuccessResponse($this->crudService->getRecordsInformation());
    }
}
