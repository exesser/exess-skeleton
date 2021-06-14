<?php

namespace ExEss\Cms\CRUD\Suggestions;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\CRUD\Factory\FieldFactory;
use ExEss\Cms\CRUD\Helpers\CrudFlowHelper;
use ExEss\Cms\CRUD\Helpers\SecurityService;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Exception\NotAllowedException;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Form\JsonField;
use ExEss\Cms\FLW_Flows\Response\Form\TextareaField;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\SaveFlow;
use ExEss\Cms\FLW_Flows\Suggestions\AbstractSuggestionHandler;

class RecordFieldSuggestion extends AbstractSuggestionHandler
{
    private const FIELD_ID = 'id';

    protected FieldFactory $fieldFactory;

    protected SecurityService $crudSecurity;

    protected EntityManager $em;

    public function __construct(
        EntityManager $em,
        FieldFactory $fieldFactory,
        SecurityService $crudSecurity
    ) {
        $this->fieldFactory = $fieldFactory;
        $this->crudSecurity = $crudSecurity;
        $this->em = $em;
    }

    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool
    {
        return CrudFlowHelper::isCrudFlow($flow->getKey())
            && $action->getEvent() === FlowAction::EVENT_INIT;
    }

    protected function doHandle(Response $response, FlowAction $action, Flow $flow): void
    {
        $model = $response->getModel();
        $recordType = CrudFlowHelper::getRecordType($model);

        if (
            \in_array($flow->getKey(), [SaveFlow::CRUD_EDIT, SaveFlow::CRUD_CREATE], true)
            && !$this->crudSecurity->canUpdate($recordType)
        ) {
            throw new NotAllowedException('Access Denied');
        }

        $readOnly = ($flow->getKey() == SaveFlow::CRUD_RECORD_DETAILS);
        $this->crudSecurity->checkIfRecordTypeAllowed($recordType);
        $entity = $this->getEntityFor($recordType, $model);
        $metadata = $this->em->getClassMetadata($recordType);

        foreach ($metadata->getAssociationNames() as $association) {
            if (!$metadata->isCollectionValuedAssociation($association)
                && $field = $this->fieldFactory->makeAssociationField($association, $metadata, $entity, $readOnly)
            ) {
                $response->getForm()->addField('r1c2', $field);
                $this->fieldFactory->setValueOnModel($field, $model, $metadata, $entity);
            }
        }

        $fieldNames = $metadata->getFieldNames();
        \usort($fieldNames, function (string $a, string $b) use ($metadata) {
            return \in_array($a, \array_merge(['id', FieldFactory::READ_ONLY_FIELDS]), true) ? -1 : 1;
        });
        foreach ($fieldNames as $property) {
            if ($field = $this->fieldFactory->makeField($property, $metadata, $readOnly)) {
                $response->getForm()->addField(
                    ($field instanceof TextareaField || $field instanceof JsonField) ? 'r1c2' : 'r1c1',
                    $field
                );
                $this->fieldFactory->setValueOnModel($field, $model, $metadata, $entity);
            }
        }
    }

    private function getEntityFor(string $recordType, Model $model): object
    {
        $recordId = $model->{self::FIELD_ID} ?? $model->{Dwp::CRUD_DUPLICATE_RECORD_ID};

        if (!empty($recordId)) {
            return $this->em->getRepository($recordType)->find($recordId);
        }

        return new $recordType;
    }
}
