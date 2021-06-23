<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Filter;

use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\Http\SuccessResponse;
use ExEss\Cms\Service\FilterService;
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
