<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\Filter;

use ExEss\Bundle\CmsBundle\Entity\ListDynamic;
use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
use ExEss\Bundle\CmsBundle\Service\FilterService;
use Symfony\Component\Routing\Annotation\Route;

class ViewController
{
    private FilterService $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    /**
     * @Route("/Api/filter/{name}", methods={"GET"})
     */
    public function __invoke(ListDynamic $list): SuccessResponse
    {
        return new SuccessResponse($this->filterService->getFilters($list));
    }
}
