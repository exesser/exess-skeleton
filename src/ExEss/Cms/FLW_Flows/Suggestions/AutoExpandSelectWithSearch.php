<?php
namespace ExEss\Cms\FLW_Flows\Suggestions;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\FESelectWithSearch\SelectWithSearchService;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;

class AutoExpandSelectWithSearch extends AbstractSuggestionHandler
{
    protected SelectWithSearchService $selectWithSearch;

    public function __construct(
        SelectWithSearchService $selectWithSearch
    ) {
        $this->selectWithSearch = $selectWithSearch;
    }

    /**
     * @inheritdoc
     */
    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool
    {
        //Only run on INIT
        if ($action->getEvent() !== FlowAction::EVENT_INIT) {
            return false;
        }
        return true;
    }

    protected function doHandle(Response $response, FlowAction $action, Flow $flow): void
    {
        // Find all selectWithSearch fields that need to be preFilled
        foreach ($response->getForm()->getGroups() as $card) {
            foreach ($card->fields as $field) {
                if ($field->type === 'selectWithSearch' && ($field->preFill ?? '' === 'all')) {
                    $options = $this->selectWithSearch->getSelectOptions(
                        $field->datasourceName,
                        ['fullModel' => $response->getModel()->toArray()]
                    );
                    $response->getModel()->setFieldValue($field->id, $options['rows']);
                }
            }
        }
    }
}
