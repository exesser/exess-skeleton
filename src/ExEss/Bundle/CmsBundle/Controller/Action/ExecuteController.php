<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\Action;

use ExEss\Bundle\CmsBundle\Entity\FlowAction;
use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
use ExEss\Bundle\CmsBundle\Service\FlowActionService;
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
     * @Route("/action/{guid}", methods={"POST"})
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
