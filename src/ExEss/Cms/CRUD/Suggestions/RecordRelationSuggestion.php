<?php

namespace ExEss\Cms\CRUD\Suggestions;

use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\SecurityGroup;
use ExEss\Cms\Manager\AssociationManager;
use ExEss\Cms\CRUD\Helpers\CrudFlowHelper;
use ExEss\Cms\CRUD\Helpers\SecurityService;
use ExEss\Cms\FLW_Flows\EnumRecord;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\SaveFlow;
use ExEss\Cms\FLW_Flows\Suggestions\AbstractSuggestionHandler;

class RecordRelationSuggestion extends AbstractSuggestionHandler
{
    protected SecurityService $crudSecurity;

    protected AssociationManager $associationManager;

    public function __construct(
        AssociationManager $associationManager,
        SecurityService $crudSecurity
    ) {
        $this->crudSecurity = $crudSecurity;
        $this->associationManager = $associationManager;
    }

    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool
    {
        return $flow->getKey() === SaveFlow::CRUD_RECORD_DETAILS
            && $action->getEvent() === FlowAction::EVENT_INIT
            && !$response->getModel()->hasNonEmptyValueFor(Dwp::RELATIONS_FIELD);
    }

    protected function doHandle(Response $response, FlowAction $action, Flow $flow): void
    {
        $model = $response->getModel();
        $recordType = CrudFlowHelper::getRecordType($model);
        $relations = $this->getRelations($recordType);
        $model->setFieldValue(Dwp::RELATIONS_FIELD, $relations);

        $relationModel = new Model();
        foreach ($relations as $relation) {
            $relationModel->{$relation->getKey()} = [Dwp::RECORD_TYPE => $relation->getValue()];
        }

        $model->setFieldValue('Reading', $relationModel);

        if ($model->hasNonEmptyValueFor(Dwp::RELATIONS_FIELD)) {
            $response->setForceReload(true);
        }
    }

    /**
     * @return array|EnumRecord[]
     */
    private function getRelations(string $module): array
    {
        $relations = $this->associationManager->getCollectionValuedAssociationsFor($module);
        $relationshipList = [];

        // display SecurityGroup at the end
        \uasort(
            $relations,
            function ($a, $b) {
                return $b === SecurityGroup::class ? -1 : 1;
            }
        );
        foreach ($relations as $field => $target) {
            $relationshipList[] = new EnumRecord($field, $target);
        }

        return $relationshipList;
    }
}
