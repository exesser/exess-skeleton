<?php

namespace ExEss\Bundle\CmsBundle\CRUD\Helpers;

use ExEss\Bundle\CmsBundle\Dictionary\Model\Dwp;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Component\Flow\SaveFlow;

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
