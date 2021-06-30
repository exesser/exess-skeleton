<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\ListDynamic;

use ExEss\Bundle\CmsBundle\Entity\ListDynamic;
use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
use ExEss\Bundle\CmsBundle\Service\ListRowBarService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;

class RowBarController
{
    private ListRowBarService $service;

    public function __construct(
        ListRowBarService $repository
    ) {
        $this->service = $repository;
    }

    /**
     * @Route("/Api/list/{name}/row/bar/{recordId}", methods={"POST"})
     * @ParamConverter("jsonBody")
     */
    public function __invoke(ListDynamic $list, string $recordId, Body\RowBarBody $jsonBody): SuccessResponse
    {
        return new SuccessResponse([
            'buttons' => $this->service->findListRowActions($list, $recordId, $jsonBody->getActionData()),
        ]);
    }
}
