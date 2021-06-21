<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\ListDynamic;

use ExEss\Cms\Api\V8_Custom\Params\ListParams;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\Http\SuccessResponse;
use ExEss\Cms\Service\ListService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ViewController
{
    private ListService $listHandler;
    private ListParams $listParams;

    public function __construct(
        ListService $listHandler,
        ListParams $listParams
    ) {
        $this->listHandler = $listHandler;
        $this->listParams = $listParams;
    }

    /**
     * @Route("/Api/list/{name}", methods={"POST"})
     */
    public function __invoke(Request $request, ListDynamic $list): SuccessResponse
    {
        $this->listParams->configure(\json_decode($request->getContent(), true));

        return new SuccessResponse($this->listHandler->getList($list, $this->listParams));
    }
}
