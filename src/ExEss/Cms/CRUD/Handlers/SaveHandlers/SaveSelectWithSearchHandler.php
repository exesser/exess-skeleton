<?php

namespace ExEss\Cms\CRUD\Handlers\SaveHandlers;

use ExEss\Cms\CRUD\Helpers\CrudFlowHelper;
use ExEss\Cms\Component\Flow\Handler\AbstractSaveHandler;
use ExEss\Cms\Component\Flow\Handler\FlowData;

class SaveSelectWithSearchHandler extends AbstractSaveHandler
{
    public static function shouldHandle(FlowData $data): bool
    {
        return CrudFlowHelper::isCrudFlow($data->getFlowKey());
    }

    protected function doHandle(FlowData $data): void
    {
        $model = $data->getModel();

        foreach ($model->toArray() as $fieldId => $fieldValue) {
            if (\is_array($fieldValue) && \count($fieldValue) === 1 && isset($fieldValue[0]['key'])) {
                $model->offsetSet($fieldId, $fieldValue[0]['key']);
            }
        }
    }
}
