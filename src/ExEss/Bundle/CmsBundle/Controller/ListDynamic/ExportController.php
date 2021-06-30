<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\ListDynamic;

use ExEss\Bundle\CmsBundle\Entity\ListDynamic;
use ExEss\Bundle\CmsBundle\Component\Flow\Action\Command;
use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
use ExEss\Bundle\CmsBundle\Service\ListExportService;
use ExEss\Bundle\CmsBundle\Service\ListService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
    public function __invoke(ListDynamic $list, Body\ListBody $jsonBody): SuccessResponse
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
