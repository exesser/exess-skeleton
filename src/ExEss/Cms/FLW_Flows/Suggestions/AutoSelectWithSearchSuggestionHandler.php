<?php

namespace ExEss\Cms\FLW_Flows\Suggestions;

use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Exception\ExternalListFetchException;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\Service\SelectWithSearchService;

class AutoSelectWithSearchSuggestionHandler extends AbstractSuggestionHandler
{
    protected SelectWithSearchService $selectWithSearchService;

    public function __construct(SelectWithSearchService $selectWithSearchService)
    {
        $this->selectWithSearchService = $selectWithSearchService;
    }

    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool
    {
        return $response->getModel()
            && \count(self::getSelectWithSearchFields($response));
    }

    protected function doHandle(Response $response, FlowAction $action, Flow $flow): void
    {
        foreach (self::getSelectWithSearchFields($response) as $field) {
            try {
                $selectWithSearchRows = $this->selectWithSearchService->getSelectOptions(
                    $field->datasourceName,
                    $response->getModel()
                );

                if (\count($selectWithSearchRows['rows'] ?? []) === 1) {
                    $response->getModel()->setFieldValue($field->id, $selectWithSearchRows['rows']);
                }
            } catch (ExternalListFetchException $e) {
                //when we can not resolve the SWS, we simply do not default anything.
            }
        }
    }

    private static function getSelectWithSearchFields(Response $response): array
    {
        $swsFields = [];
        foreach ($response->getForm()->getFields() as $field) {
            if (
                $field->type === FlowFieldType::FIELD_TYPE_SELECT_WITH_SEARCH
                && $field->auto_select_suggestions
                && !$response->getModel()->hasNonEmptyValueFor($field->id)

            ) {
                $swsFields[] = $field;
            }
        }

        return $swsFields;
    }
}
