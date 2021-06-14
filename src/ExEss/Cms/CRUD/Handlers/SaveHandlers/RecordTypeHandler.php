<?php

namespace ExEss\Cms\CRUD\Handlers\SaveHandlers;

use ExEss\Cms\CRUD\Helpers\CrudFlowHelper;
use ExEss\Cms\FLW_Flows\Handler\AbstractSaveHandler;
use ExEss\Cms\FLW_Flows\Handler\FlowData;

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
