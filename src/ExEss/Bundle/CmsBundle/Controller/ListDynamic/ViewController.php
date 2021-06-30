<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\ListDynamic;

use ExEss\Bundle\CmsBundle\Entity\ListDynamic;
use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
use ExEss\Bundle\CmsBundle\Service\ListService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;

class ViewController
{
    private ListService $listHandler;

    public function __construct(
        ListService $listHandler
    ) {
        $this->listHandler = $listHandler;
    }

    /**
     * @Route("/Api/list/{name}", methods={"POST"})
     * @ParamConverter("jsonBody")
     */
    public function __invoke(ListDynamic $list, Body\ListBody $jsonBody): SuccessResponse
    {
        return new SuccessResponse($this->listHandler->getList($list, $jsonBody));
    }
}
