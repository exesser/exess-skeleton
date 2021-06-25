<?php

namespace ExEss\Cms\FLW_Flows\Suggestions;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\FLW_Flows\Field;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\Component\ExpressionParser\ParserService;

class OverrideDefaultHandler extends AbstractSuggestionHandler
{
    protected Field $field;

    protected ParserService $parserService;

    public function __construct(
        Field $field,
        ParserService $parserService
    ) {
        $this->field = $field;
        $this->parserService = $parserService;
    }

    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool
    {
        return true;
    }

    protected function doHandle(Response $response, FlowAction $action, Flow $flow): void
    {
        $model = $response->getModel();

        foreach ($flow->getSteps() as $step) {
            foreach ($this->field->getFieldGroupsFromFields($step->getFields()) as $fields) {
                foreach ($fields as $field) {
                    if (!empty($field->overwrite_value)) {
                        if (!empty($model->offsetGet(\str_replace('%', '', $field->overwrite_value)))) {
                            $newValue = $model->offsetGet(\str_replace('%', '', $field->overwrite_value));
                            if ($newValue instanceof Model) {
                                $newValue = $newValue->getFirstKeyValue();
                            }
                            $model->offsetSet(
                                $field->id,
                                $newValue
                            );
                            continue;
                        }
                        $model->offsetSet(
                            $field->id,
                            $this->parserService->parseListValue(
                                $model,
                                $field->overwrite_value,
                                null
                            )
                        );
                    }
                }
            }
        }
    }
}
