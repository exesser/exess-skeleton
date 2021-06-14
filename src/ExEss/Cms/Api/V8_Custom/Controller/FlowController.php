<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiFlashMessageController;
use ExEss\Cms\Api\V8_Custom\Params\FlowUpdateParams;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Cms\FLW_Flows\Action\BackendCommandExecutor;
use ExEss\Cms\FLW_Flows\Action\Command;
use ExEss\Cms\FLW_Flows\Event\FlowEventDispatcher;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response\Model;
use Symfony\Component\Translation\Translator;

class FlowController extends AbstractApiFlashMessageController
{
    private BackendCommandExecutor $backendCommandExecutor;

    private FlowEventDispatcher $flowEventDispatcher;

    public function __construct(
        FlashMessageContainer $flashMessageContainer,
        FlowEventDispatcher $flowEventDispatcher,
        BackendCommandExecutor $backendCommandExecutor,
        Translator $translator
    ) {
        parent::__construct($flashMessageContainer, $translator);

        $this->backendCommandExecutor = $backendCommandExecutor;
        $this->flowEventDispatcher = $flowEventDispatcher;
    }

    public function getFlowUpdate(Request $req, Response $res, array $args, FlowUpdateParams $params): Response
    {
        $response = $this->flowEventDispatcher->dispatch(
            $params->getFlow()->getKey(),
            $params->getAction(),
            $model = new Model($params->getModel()),
            $params->getParentModel(),
            $params->getParams(),
            $params->getRecordType(),
            $params->getGuidanceAction(),
            $params->getRoute()
        );

        if ($params->getGuidanceAction() === FlowAction::BREAKOUT) {
            $this->addFlashMessage(
                'The account you provided already exists, ' .
                'you were redirected to create a quote for this existing account',
                FlashMessage::TYPE_WARNING
            );
        }

        $statusCode = 200;
        if ($response instanceof Command) {
            $this->backendCommandExecutor->execute($response, $response->getArguments()->recordIds, $model);
            if ($response->getRelatedBeans()) {
                $statusCode = 201;
            }
        }

        return $this->generateResponse(
            $res,
            $statusCode,
            $response
        );
    }
}
