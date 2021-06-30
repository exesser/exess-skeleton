<?php

namespace ExEss\Bundle\CmsBundle\Component\Flow\Event\Listeners;

use Doctrine\ORM\EntityManager;
use ExEss\Bundle\CmsBundle\Dictionary\Model\Dwp;
use ExEss\Bundle\CmsBundle\Entity\Flow;
use ExEss\Bundle\CmsBundle\Entity\FlowField;
use ExEss\Bundle\CmsBundle\Component\Flow\Builder\FormBuilder;
use ExEss\Bundle\CmsBundle\Component\Flow\Event\FlowEvent;
use ExEss\Bundle\CmsBundle\Component\Flow\Event\FlowEvents;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Component\Flow\SaveFlow;
use ExEss\Bundle\CmsBundle\Logger\Logger;
use ExEss\Bundle\CmsBundle\Service\GridService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ModelCleanerSubscriber implements EventSubscriberInterface
{
    private FormBuilder $formBuilder;

    /**
     * Prefixes that are allowed to pass as they are dynamically created form fields
     */
    private array $whiteListPrefix = [
        Dwp::ROW_OPTIONS_PREFIX,
    ];

    /**
     * Temporary skip cleaning for these flows as there are too many issues with this
     * @todo remove this
     */
    private array $whiteListFlow = [
        SaveFlow::CRUD_EDIT,
        SaveFlow::CRUD_CREATE,
    ];

    private array $whiteListFields = [
        Dwp::FLAG_CONFIRM_ACTION_KEY,
        // following fields may always be on any flow and will never be cleaned (nor validated!) if they are
        // @todo validate these
        'recordIds',
        'recordId',
        'recordTypeOfRecordId',
        'id',
        'baseModule',
        Dwp::DYNAMIC_LOADED_FIELDS,
    ];

    private GridService $gridService;

    private Logger $logger;

    private EntityManager $em;

    public function __construct(
        EntityManager $em,
        FormBuilder $formBuilder,
        GridService $gridService,
        Logger $logger
    ) {
        $this->formBuilder = $formBuilder;
        $this->gridService = $gridService;
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::CONFIRM => [
                ['cleanModel', -20],  // before PreValidationSubscriber and after FormAndModelSubscriber
                ['unsetWhenEmpty', -19],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function unsetWhenEmpty(FlowEvent $event): void
    {
        $response = $event->getResponse();
        $flow = $event->getFlow();
        $model = $response->getModel();

        $fields = $this->getAllGuidanceFields($flow);

        $this->unsetOnModel($model, $fields['parent']);

        foreach ($fields['children'] as $repeatableKey => $childFields) {
            foreach ($model->getFieldValue($repeatableKey) as $childModel) {
                $this->unsetOnModel($childModel, $childFields);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function cleanModel(FlowEvent $event): void
    {
        $response = $event->getResponse();
        $flow = $event->getFlow();
        $model = $response->getModel();

        $fields = $this->getAllowedFieldsFor($flow);
        // clean the parent flow model
        $this->clean($model, $flow, $fields['parent']);

        // clean child flow models
        foreach ($fields['children'] as $nameSpace => $childFields) {
            if (!$model->$nameSpace instanceof Model) {
                continue;
            }

            // clean each child model in this namespace
            foreach ($model->$nameSpace as $childModel) {
                $this->clean($childModel, $flow, $childFields, true);
            }
        }
    }

    private function clean(Model $model, Flow $flow, array $allowedFields, bool $isChild = false): void
    {
        // get the fields in the model that are not a form field
        $diff = \array_diff(\array_keys($model->toArray()), $allowedFields);

        // whitelist a couple of fields and field prefixes that we allow to stay in
        $diff = \array_filter($diff, function ($value) {
            return !\in_array($value, $this->whiteListFields, true) && !$this->hasWhitelistedPrefix($value);
        });

        // prepare the diff we are going to log
        $loggedDiff = \array_filter($diff, function ($value) {
            return \strpos($value, '|matchedCondition') === false;
        });
        if (empty($loggedDiff)) {
            return;
        }
        \sort($loggedDiff);
        $loggedDiff = \json_encode(\array_values($loggedDiff));

        // for now, white list some flows since they are missing way too many fields in their form
        if (!\in_array($flow->getKey(), $this->whiteListFlow, true)) {
            $this->logger->info(\sprintf(
                'ModelCleaner got a "CONFIRM" for "%s" and removed these fields from the %s model: %s',
                $flow->getKey(),
                $isChild ? 'child' : 'parent',
                $loggedDiff
            ));
            // remove anything that doesn't belong in the model and should be (re)calculated upon CONFIRM
            foreach ($diff as $fieldToRemove) {
                $model->offsetUnset($fieldToRemove);
            }
        } else {
            $this->logger->info(\sprintf(
                'ModelCleaner got a "CONFIRM" for "%s" but the parent flow was whitelisted, '
                .'if we would have, these would have been stripped: %s',
                $flow->getKey(),
                $loggedDiff
            ));
        }
    }

    private function getAllowedFieldsFor(Flow $flow): array
    {
        $parentFields = [];
        $childFields = [];

        foreach ($flow->getSteps() as $step) {
            $parentFields = \array_merge($parentFields, $this->formBuilder->getFlowStepFields($step));

            foreach ($this->gridService->getRepeatableRowsInStep($step) as $repeatableRow) {
                $nameSpace = $repeatableRow->getOptions()->getModelKey();
                $parentFields[] = $nameSpace;

                /** @var Flow $repeatedFlow */
                $repeatedFlow = $this->em->getRepository(Flow::class)->get(
                    $repeatableRow->getOptions()->getFlowId()
                );

                foreach ($repeatedFlow->getSteps() as $repeatedFlowStep) {
                    $childFields[$nameSpace] = \array_merge(
                        $childFields[$nameSpace] ?? [],
                        $this->formBuilder->getFlowStepFields($repeatedFlowStep)
                    );
                }
            }
        }

        // each child can contain any field from the parent in the same namespace
        foreach ($childFields as $nameSpace => &$fields) {
            foreach ($parentFields as $parentField) {
                if ($nameSpace !== $parentField && \strpos($parentField, $nameSpace) === 0) {
                    $fields[] = \str_replace($nameSpace . '|', '', $parentField);
                }
            }
        }

        /**
         * special case, when this field exists, 2 other fields are on the model
         * @see Dwp::CONTACT_PERSON_GROUP for usages where this is done
         */
        if (\in_array(Dwp::CONTACT_PERSON_GROUP, $parentFields, true)) {
            unset($parentFields[Dwp::CONTACT_PERSON_GROUP]);
            $parentFields[] = 'first_name';
            $parentFields[] = 'last_name';
        }

        // only keep the unique field names
        $parentFields = \array_unique($parentFields);
        foreach ($childFields as &$fields) {
            $fields = \array_unique($fields);
        }

        return [
            'parent' => $parentFields,
            'children' => $childFields,
        ];
    }

    private function getAllGuidanceFields(Flow $flow): array
    {
        $childFields = [];
        $parentFields = $this->getGuidanceFields($flow);

        foreach ($this->gridService->getRepeatableRows($flow) as $repeatableRow) {
            /** @var Flow $childFlow */
            $childFlow = $this->em->getRepository(Flow::class)->get(
                $repeatableRow->getOptions()->getFlowId()
            );

            $childFields = \array_merge(
                $childFields,
                [$repeatableRow->getOptions()->getModelKey() => $this->getGuidanceFields($childFlow)]
            );
        }

        return [
            'parent' => $parentFields,
            'children' => $childFields,
        ];
    }

    /**
     * @param array|FlowField[] $fields
     */
    private function unsetOnModel(Model $model, array $fields): void
    {
        foreach ($fields as $field) {
            $fieldId = $field->getFieldId();

            if ($field->isRemoveWhenEmpty() && empty($model->getFieldValue($fieldId, '', true))) {
                $model->offsetUnset($fieldId);
            }
        }
    }

    private function hasWhitelistedPrefix(string $property): bool
    {
        foreach ($this->whiteListPrefix as $prefix) {
            if (\strpos($property, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array|FlowField[]
     */
    private function getGuidanceFields(Flow $flow): array
    {
        $fields = [];
        foreach ($flow->getSteps() as $step) {
            $fields = \array_merge($fields, $step->getFields()->getValues());
        }

        return $fields;
    }
}
