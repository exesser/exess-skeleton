<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Action;

use ExEss\Cms\Entity\FlowAction;
use ExEss\Cms\Http\SuccessResponse;
use ExEss\Cms\Service\FlowActionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;

class ExecuteController
{
    private FlowActionService $flowActionService;

    public function __construct(
        FlowActionService $flowActionService
    ) {
        $this->flowActionService = $flowActionService;
    }

    /**
     * @Route("/Api/action/{guid}", methods={"POST"})
     * @ParamConverter("jsonBody")
     */
    public function __invoke(FlowAction $action, Body\ExecuteBody $jsonBody): SuccessResponse
    {
        return new SuccessResponse(
            $this->flowActionService->execute(
                $action,
                $jsonBody->getParams(),
                $jsonBody->getActionData(),
                $jsonBody->getRecordType(),
                $jsonBody->getRecordId(),
                $jsonBody->getRecordIds()
            )
        );
    }
}
