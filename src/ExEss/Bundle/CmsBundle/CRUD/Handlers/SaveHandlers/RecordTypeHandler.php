<?php

namespace ExEss\Bundle\CmsBundle\CRUD\Handlers\SaveHandlers;

use ExEss\Bundle\CmsBundle\CRUD\Helpers\CrudFlowHelper;
use ExEss\Bundle\CmsBundle\Component\Flow\Handler\AbstractSaveHandler;
use ExEss\Bundle\CmsBundle\Component\Flow\Handler\FlowData;

class RecordTypeHandler extends AbstractSaveHandler
{
    public static function shouldHandle(FlowData $data): bool
    {
        return CrudFlowHelper::isCrudFlow($data->getFlowKey());
    }

    protected function doHandle(FlowData $data): void
    {
        $model = $data->getModel();
        $flow = $data->getFlow();
        $recordType = CrudFlowHelper::getRecordType($model);
        $model->baseModule = $recordType;
        $flow->setBaseObject($recordType);
    }
}
