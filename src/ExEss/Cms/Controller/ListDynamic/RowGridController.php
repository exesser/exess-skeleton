<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\ListDynamic;

use ExEss\Cms\Entity\GridTemplate;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\Http\SuccessResponse;
use ExEss\Cms\Service\GridService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;

class RowGridController
{
    private GridService $gridService;

    public function __construct(GridService $gridService)
    {
        $this->gridService = $gridService;
    }

    /**
     * @Route("/Api/list/{list_name}/row/grid/{grid_key}/{recordId}", methods={"POST"})
     * @ParamConverter("jsonBody")
     * @ParamConverter("list", options={"mapping": {"list_name": "name"}})
     * @ParamConverter("grid", options={"mapping": {"grid_key": "key"}})
     */
    public function __invoke(
        ListDynamic $list,
        GridTemplate $grid,
        string $recordId,
        Body\RowGridBody $jsonBody
    ): SuccessResponse {
        return new SuccessResponse([
            'grid' => $this->gridService->getGridByKey(
                $grid,
                [
                    'listKey' => $list->getName(),
                    'recordId' => $recordId,
                    'actionData' => $jsonBody->getActionData(),
                ] + $jsonBody->getActionData()
            )
        ]);
    }
}
