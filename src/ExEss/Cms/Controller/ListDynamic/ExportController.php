<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\ListDynamic;

use ExEss\Cms\Api\V8_Custom\Params\ListParams;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\FLW_Flows\Action\Command;
use ExEss\Cms\Http\SuccessResponse;
use ExEss\Cms\Service\ListExportService;
use ExEss\Cms\Service\ListService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExportController
{
    private ListService $listHandler;
    private ListExportService $exportService;
    private ListParams $listParams;

    public function __construct(
        ListService $listHandler,
        ListExportService $exportService,
        ListParams $listParams
    ) {
        $this->listHandler = $listHandler;
        $this->exportService = $exportService;
        $this->listParams = $listParams;
    }

    /**
     * @Route("/Api/list/{name}/export/csv", methods={"POST"})
     */
    public function __invoke(Request $request, ListDynamic $list): SuccessResponse
    {
        $this->listParams->configure(\json_decode($request->getContent(), true));

        $link = $this->exportService->export(
            $this->listHandler->getList($list, $this->listParams)
        );

        return new SuccessResponse([
            'command' => Command::COMMAND_TYPE_OPEN_LINK,
            'arguments' => [
                'link' => $link,
            ],
        ]);
    }
}
