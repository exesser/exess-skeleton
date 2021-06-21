<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\ListDynamic;

use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\FLW_Flows\Action\Command;
use ExEss\Cms\Http\SuccessResponse;
use ExEss\Cms\Service\ListExportService;
use ExEss\Cms\Service\ListService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExportController
{
    private ListService $listHandler;
    private ListExportService $exportService;

    public function __construct(
        ListService $listHandler,
        ListExportService $exportService
    ) {
        $this->listHandler = $listHandler;
        $this->exportService = $exportService;
    }

    /**
     * @Route("/Api/list/{name}/export/csv", methods={"POST"})
     * @ParamConverter("jsonBody")
     */
    public function __invoke(Request $request, ListDynamic $list, Body\ListBody $jsonBody): SuccessResponse
    {
        $link = $this->exportService->export(
            $this->listHandler->getList($list, $jsonBody)
        );

        return new SuccessResponse([
            'command' => Command::COMMAND_TYPE_OPEN_LINK,
            'arguments' => [
                'link' => $link,
            ],
        ]);
    }
}
