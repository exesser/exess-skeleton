<?php

namespace ExEss\Cms\CRUD\Helpers;

use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\SaveFlow;

class CrudFlowHelper
{
    public static function getRecordType(Model $model): string
    {
        return $model->getFieldValue(Dwp::RECORD_TYPE_OF_RECORD_ID);
    }

    public static function isCrudFlow(string $flowKey): bool
    {
        return \in_array($flowKey, SaveFlow::CRUD_RECORD_FLOWS, true);
    }
}
