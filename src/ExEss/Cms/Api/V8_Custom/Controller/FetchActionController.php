<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Params\ActionParams;
use ExEss\Cms\FLW_Flows\Action\BackendCommandExecutor;
use ExEss\Cms\FLW_Flows\ActionFactory;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\ListFunctions\HelperClasses\ListHelperFunctions;

class FetchActionController extends AbstractApiController
{
    private BackendCommandExecutor $backendCommandExecutor;

    private ActionFactory $actionFactory;

    private ListHelperFunctions $listHelper;

    private EntityManagerInterface $em;

    public function __construct(
        ActionFactory $actionFactory,
        BackendCommandExecutor $backendCommandExecutor,
        EntityManagerInterface $em,
        ListHelperFunctions $listHelperFunctions
    ) {
        $this->backendCommandExecutor = $backendCommandExecutor;
        $this->actionFactory = $actionFactory;
        $this->listHelper = $listHelperFunctions;
        $this->em = $em;
    }

    /**
     * @throws \LogicException When stuff goes wrong.
     */
    public function __invoke(Request $req, Response $res, array $args, ActionParams $actionParams): Response
    {
        $params = $actionParams->getParams();
        $recordType = $params['recordType'] ?? $actionParams->getRecordType();
        $recordIds = [];

        if (!$actionParams->getRecordIds() || isset($params['recordId']) || !$actionParams->getRecordId()) {
            $recordIds = $actionParams->getRecordIds()
                ?? [$params['recordId'] ?? $actionParams->getRecordId()];
            $params['recordIds'] = $recordIds;
        }

        //If the $params['fetchRecordId'] is set, apply it on each recordId before handing it on
        if (!empty($params['fetchRecordId']) && !empty($recordIds) && !empty($params['recordType']) ) {
            foreach ($recordIds as $recordId) {
                $baseEntity = $this->em->getRepository($params['recordType'])->find($recordId);
                $newRecordIds[] = $this->listHelper->parseListValue($baseEntity, $params['fetchRecordId']);
            }
            $recordIds = $newRecordIds;
        }

        // We copy all values from the actionData to the params here so we can use them in the row action config
        $params = \array_merge($params, $actionParams->getActionData() ?? []);

        $command = $this->actionFactory->getCommand($actionParams->getAction(), $recordIds, $recordType, $params);

        if ($command === null) {
            throw new \LogicException('unknown command: '.$actionParams->getAction()->getGuid());
        }

        $model = null;
        if (!empty($command->getArguments()->model)) {
            $model = $command->getArguments()->model;
            if (!$model instanceof Model) {
                $model = new Model($model);
            }
        }

        $this->backendCommandExecutor->execute($command, $recordIds, $model);

        return $this->generateResponse($res, 200, $command->toArray());
    }
}
