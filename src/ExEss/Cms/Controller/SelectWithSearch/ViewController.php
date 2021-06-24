<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\SelectWithSearch;

use ExEss\Cms\Entity\SelectWithSearch;
use ExEss\Cms\Http\SuccessResponse;
use ExEss\Cms\Service\SelectWithSearchService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;

class ViewController
{
    private SelectWithSearchService $selectWithSearchService;

    public function __construct(
        SelectWithSearchService $selectWithSearchService
    ) {
        $this->selectWithSearchService = $selectWithSearchService;
    }

    /**
     * @Route("/Api/select-with-search/{name}", methods={"POST"})
     * @ParamConverter("jsonBody")
     */
    public function __invoke(SelectWithSearch $selectWithSearch, Body\ViewBody $jsonBody): SuccessResponse
    {
        return new SuccessResponse($this->selectWithSearchService->getSelectOptions(
            $selectWithSearch->getName(),
            $jsonBody->getFullModel(),
            $jsonBody->getPage(),
            $jsonBody->getQuery(),
            $jsonBody->getKeys(),
            $jsonBody->getParams()['baseObject'] ?? null
        ));
    }
}
