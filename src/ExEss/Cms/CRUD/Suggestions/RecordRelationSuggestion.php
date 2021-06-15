<?php declare(strict_types=1);

namespace ExEss\Cms\CRUD\Suggestions;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\SecurityGroup;
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

    protected EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        SecurityService $crudSecurity
    ) {
        $this->crudSecurity = $crudSecurity;
        $this->em = $em;
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
        $associations = $this->getRelations($this->em->getClassMetadata($recordType));

        $model->setFieldValue(Dwp::RELATIONS_FIELD, $associations);

        $relationModel = new Model();
        foreach ($associations as $association) {
            $relationModel->{$association->getKey()} = [Dwp::RECORD_TYPE => $association->getValue()];
        }

        $model->setFieldValue('Reading', $relationModel);

        if ($model->hasNonEmptyValueFor(Dwp::RELATIONS_FIELD)) {
            $response->setForceReload(true);
        }
    }

    /**
     * @return array|EnumRecord[]
     */
    private function getRelations(ClassMetadata $metadata): array
    {
        $associations = [];
        foreach ($metadata->getAssociationNames() as $fieldName) {
            if ($metadata->isCollectionValuedAssociation($fieldName)) {
                $associations[] = new EnumRecord($fieldName, $metadata->getAssociationTargetClass($fieldName));
            }
        }

        // display SecurityGroup at the end
        \uasort(
            $associations,
            function (EnumRecord $a, EnumRecord $b) {
                return $b->getValue() === SecurityGroup::class ? -1 : 1;
            }
        );

        return \array_values($associations);
    }
}
