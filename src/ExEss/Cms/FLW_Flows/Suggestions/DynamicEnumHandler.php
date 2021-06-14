<?php
namespace ExEss\Cms\FLW_Flows\Suggestions;

use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Doctrine\Type\GeneratedFieldType;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\FLW_Flows\Builder\EnumFieldBuilder;
use ExEss\Cms\FLW_Flows\EnumRecordFactory;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Suggestion\ValueSuggestion;

class DynamicEnumHandler extends AbstractSuggestionHandler
{
    protected EnumRecordFactory $enumRecordFactory;

    protected EnumFieldBuilder $enumFieldBuilder;

    public function __construct(EnumRecordFactory $enumRecordFactory, EnumFieldBuilder $enumFieldBuilder)
    {
        $this->enumRecordFactory = $enumRecordFactory;
        $this->enumFieldBuilder = $enumFieldBuilder;
    }

    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool
    {
        return $response->getForm() !== null;
    }

    protected function doHandle(Response $response, FlowAction $action, Flow $flow): void
    {
        $suggestions = $response->getSuggestions();

        foreach ($response->getForm()->getGroups() as $group) {
            foreach ($group->fields as $field) {
                if ($field->type === FlowFieldType::FIELD_TYPE_ENUM
                    && isset($field->enumValues)
                ) {
                    $enumValuesFromConditional = $this->enumFieldBuilder->expandIfConditionalEnum(
                        $field,
                        $response->getModel(),
                        true
                    );

                    $enumValuesFromSwS = $this->enumFieldBuilder->expandIfSelectWIthSearchBase(
                        $field,
                        $response
                    );

                    if ($enumValuesFromConditional === null
                        && $enumValuesFromSwS === null
                    ) {
                        // not a conditional enum
                        continue;
                    }

                    foreach ($enumValuesFromConditional ?? $enumValuesFromSwS as $record) {
                        // add as suggestion
                        $suggestions->addFor($field->id, new ValueSuggestion($record->key, $record->value));
                    }

                    if (
                        $action->getEvent() === FlowAction::EVENT_INIT
                        && ($field->auto_select_all_suggestions ?? false)
                        && !$response->getModel()->hasNonEmptyValueFor($field->id)
                        && $suggestions->getFor($field->id)->count()
                    ) {
                        $response->getModel()->setFieldValue(
                            $field->id,
                            \array_map(
                                function (ValueSuggestion $suggestion) {
                                    return $suggestion->getValue();
                                },
                                $suggestions->getFor($field->id)->getArrayCopy()
                            )
                        );

                        if ($field->generateType === GeneratedFieldType::REPEAT_TRIGGER) {
                            $response->setForceReload(true);
                        }
                    }
                }
            }
        }
    }
}
