<?php

namespace ExEss\Bundle\CmsBundle\CRUD\Suggestions;

use ExEss\Bundle\CmsBundle\CRUD\Helpers\CrudFlowHelper;
use ExEss\Bundle\CmsBundle\Entity\Flow;
use ExEss\Bundle\CmsBundle\Component\Flow\Request\FlowAction;
use ExEss\Bundle\CmsBundle\Component\Flow\Response;
use ExEss\Bundle\CmsBundle\Component\Flow\Suggestions\AbstractSuggestionHandler;

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
