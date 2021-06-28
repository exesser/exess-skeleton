<?php

namespace ExEss\Cms\CRUD\Suggestions;

use ExEss\Cms\CRUD\Helpers\CrudFlowHelper;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Component\Flow\Request\FlowAction;
use ExEss\Cms\Component\Flow\Response;
use ExEss\Cms\Component\Flow\Suggestions\AbstractSuggestionHandler;

class RecordTypeSuggestion extends AbstractSuggestionHandler
{
    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool
    {
        return CrudFlowHelper::isCrudFlow($flow->getKey());
    }

    protected function doHandle(Response $response, FlowAction $action, Flow $flow): void
    {
        $model = $response->getModel();
        $recordType = CrudFlowHelper::getRecordType($model);
        $model->baseModule = $recordType;
        $flow->setBaseObject($recordType);
    }
}
