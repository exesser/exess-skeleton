<?php declare(strict_types=1);

namespace ExEss\Cms\Service;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Entity\FlowAction;
use ExEss\Cms\Exception\NotFoundException;
use ExEss\Cms\Component\Flow\Action\BackendCommandExecutor;
use ExEss\Cms\Component\Flow\Action\Command;
use ExEss\Cms\Component\Flow\ActionFactory;
use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Cms\Component\ExpressionParser\ParserService;

class FlowActionService
{
    private BackendCommandExecutor $backendCommandExecutor;
    private ActionFactory $actionFactory;
    private ParserService $parserService;
    private EntityManagerInterface $em;

    public function __construct(
        ActionFactory $actionFactory,
        BackendCommandExecutor $backendCommandExecutor,
        EntityManagerInterface $em,
        ParserService $parserService
    ) {
        $this->backendCommandExecutor = $backendCommandExecutor;
        $this->actionFactory = $actionFactory;
        $this->parserService = $parserService;
        $this->em = $em;
    }

    public function execute(
        FlowAction $action,
        array $params,
        array $actionData,
        ?string $recordType,
        ?string $recordId,
        ? array $recordIds
    ): Command {
        $parsedRecordIds = [];
        if (!$recordIds
            || isset($params['recordId'])
            || !$recordId
        ) {
            $parsedRecordIds = $recordIds ?? [$params['recordId'] ?? $recordId];
            $params['recordIds'] = $parsedRecordIds;
        }

        // if the $params['fetchRecordId'] is set, apply it on each recordId before handing it on
        if (!empty($params['fetchRecordId'])
            && !empty($params['recordType'])
        ) {
            foreach ($parsedRecordIds as &$recordId) {
                $baseEntity = $this->em->getRepository($params['recordType'])->find($recordId);
                $recordId = $this->parserService->parseListValue($baseEntity, $params['fetchRecordId']);
            }
        }

        $command = $this->actionFactory->getCommand(
            $action,
            $parsedRecordIds,
            $recordType,
            // We copy all values from the actionData to the params here so we can use them in the row action config
            \array_merge($params, $actionData)
        );

        if ($command === null) {
            throw new NotFoundException('Unknown command: ' . $action->getGuid());
        }

        $model = null;
        if (!empty($command->getArguments()->model)) {
            $model = $command->getArguments()->model;
            if (!$model instanceof Model) {
                $model = new Model($model);
            }
        }

        $this->backendCommandExecutor->execute($command, $parsedRecordIds, $model);

        return $command;
    }
}
