<?php
namespace ExEss\Cms\Component\Flow\Event\Listeners;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Dictionary\Format;
use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\FlowStep;
use ExEss\Cms\Component\Flow\Builder\FormBuilder;
use ExEss\Cms\Component\Flow\DefaultValueService;
use ExEss\Cms\Component\Flow\Event\FlowEvent;
use ExEss\Cms\Component\Flow\Event\FlowEvents;
use ExEss\Cms\Component\Flow\Response;
use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Cms\Logger\Logger;
use ExEss\Cms\Service\GridService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormAndModelSubscriber implements EventSubscriberInterface
{
    private const DATETIME_NOW = 'NOW';
    private const DATE_TODAY = 'TODAY';

    private FormBuilder $formBuilder;

    private DefaultValueService $defaultValueService;

    private Logger $logger;

    private GridService $gridService;

    private EntityManager $em;

    public function __construct(
        EntityManager $em,
        FormBuilder $formBuilder,
        DefaultValueService $defaultValueService,
        GridService $gridService,
        Logger $logger
    ) {
        $this->formBuilder = $formBuilder;
        $this->defaultValueService = $defaultValueService;
        $this->logger = $logger;
        $this->gridService = $gridService;
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::INIT => [
                ['getNextFormAndFillModel', 0],
                ['setParentModel', 0],
                ['expandLabelAndActionFromModel', -90], //after Suggestions and ValueExpressions
            ],
            FlowEvents::INIT_CHILD_FLOW => [
                ['getNextFormAndFillModel', 0],
                ['setParentModel', 0],
            ],
            FlowEvents::NEXT_STEP => [
                ['getNextForm', -10],
                ['setParentModel', 0],
                ['expandLabelAndActionFromModel', -90], //after Suggestions and ValueExpressions
            ],
            FlowEvents::NEXT_STEP_FORCED => [
                ['getNextForm', 0],
                ['setParentModel', 0],
                ['expandLabelAndActionFromModel', -90], //after Suggestions and ValueExpressions
            ],
            FlowEvents::CHANGED => [
                ['getCurrentFormIfPossible', 0],
                ['setParentModel', 0],
                ['removeFormFromResponse', -75],
                ['expandLabelAndActionFromModel', -90], //after Suggestions and ValueExpressions
            ],
            FlowEvents::CONFIRM_CREATE_LIST_ROW => [
                ['getCurrentFormIfPossible', 0],
                ['removeFormFromResponse', -75],
            ],
            FlowEvents::CONFIRM => [
                ['setParentModel', 0],
                ['registerModelCopyInResponse', -10],
                ['updateModelValuesBaseOnFieldType', -50],  // after validation!
                ['removeModelFromResponse', -500],
            ],
        ];
    }

    /**
     * Registers the parent model in the response
     */
    public function setParentModel(FlowEvent $event): void
    {
        if ($event->getParentModel()) {
            $event->getResponse()->setParentModel($event->getParentModel());
        }
    }

    /**
     * Registers the model in the response
     */
    public function registerModelCopyInResponse(FlowEvent $event): void
    {
        // this puts a copy of the model in the response, in case validation fails
        // you want the original model to be returned in the response not a reformatted one
        $event->getResponse()->setModel(clone $event->getModel());
    }

    public function removeModelFromResponse(FlowEvent $event): void
    {
        $event->getResponse()->setModel(new Response\Model());
    }

    public function updateModelValuesBaseOnFieldType(FlowEvent $event): void
    {
        $model = $event->getResponse()->getModel();

        foreach ($this->gridService->getRepeatableRows($event->getFlow()) as $repeatableRow) {
            $options = $repeatableRow->getOptions();
            $modelKey = $options->getModelKey();

            if (!$model->$modelKey instanceof Model) {
                continue;
            }

            $childModels = $model->$modelKey;

            /** @var Flow $childFlow */
            $childFlow = $this->em->getRepository(Flow::class)->get($options->getFlowId());

            foreach ($childModels as $childModel) {
                $this->fixFieldsForSteps($childFlow, $childModel);
            }
        }

        $this->fixFieldsForSteps($event->getFlow(), $model);
    }

    private function fixFieldsForSteps(Flow $flow, Model $model): void
    {
        foreach ($flow->getFields() as $field) {
            if ($field->getType() === FlowFieldType::FIELD_TYPE_SELECT_WITH_SEARCH
                && !$field->isMultiple()
            ) {
                $fieldId = $field->getFieldId();
                if (isset($model->$fieldId) && $model->$fieldId instanceof Response\Model) {
                    $model->$fieldId = $model->$fieldId->getFirstKeyValue();
                }
            }
        }
    }

    public function removeFormFromResponse(FlowEvent $event): void
    {
        // removes form from response after suggestions have ran
        $event->getResponse()->setForm();
    }

    public function getCurrentFormIfPossible(FlowEvent $event): void
    {
        // store the model in the response
        $event->getResponse()->setModel($event->getModel());

        // in case there's a current step, load the form so the suggestions can access it
        // as DynamicEnumHandler needs it to perform suggestions based on the model changes
        $currentStep = $event->getResponse()->getCurrentStep();
        if (!$currentStep) {
            return;
        }

        $flowStep = $currentStep->getFlowStep();
        $form = $this->getFormForStep($flowStep, $event);

        // store a reference in the response (we will remove it at a later stage)
        $event->getResponse()->setForm($form);
    }

    public function getNextForm(FlowEvent $event): void
    {
        $flowStep = $event->getNextStep()->getFlowStep();
        $form = $this->getFormForStep($flowStep, $event);

        // store a reference in the response
        $event->getResponse()->setForm($form);
        $event->getResponse()->setModel($event->getModel());
    }

    public function getNextFormAndFillModel(FlowEvent $event): void
    {
        // load the forms from the entire flow and set the values in the model
        $allFormGroups = [];
        foreach ($event->getFlow()->getSteps() as $flowStep) {
            $form = $this->getFormForStep($flowStep, $event);
            $this->fillModel($event->getModel(), $form);
            // remove keys or array_merge will override elements!
            $allFormGroups[] = \array_values($form->getGroups());

            // if this is this the next step's form, store it in the response
            if ($event->getNextStep()->getFlowStep() === $flowStep) {
                $event->getResponse()->setForm($form);
            }
        }

        $this->defaultValueService->resolveDefaults($event->getModel(), \array_merge(...$allFormGroups));

        // store a reference in the response
        $event->getResponse()->setModel($event->getModel());
    }

    public function expandLabelAndActionFromModel(FlowEvent $event): void
    {
        if ($form = $event->getResponse()->getForm()) {
            $this->formBuilder->expandLabelAndActionFromModel($form, $event->getModel());
        }
    }

    private function getFormForStep(FlowStep $step, FlowEvent $event): Response\Form
    {
        return $this->formBuilder->getFilledFlowStepForm(
            $event->getFlow(),
            $step,
            $event->getModel(),
            $event->getBaseEntity(),
            $event->getGuidanceAction(),
            $event->getParams()
        );
    }

    private function fillModel(Response\Model $model, Response\Form $form): void
    {
        $temp = new Response\Model();

        // process all form fields
        foreach ($form->getGroups() as $name => $group) {
            if (isset($group->fields)) {
                // re-assign to $group->fields since this method changes the form fields...
                $group->fields = $this->fillModelFromFields($group->fields, $temp, $model);
            }
        }

        $model->mergeNewProperties($temp);
    }

    /**
     * This method is both changing form fields as setting the field values in the model...
     *
     * @throws \InvalidArgumentException In case a field has an empty id.
     */
    public function fillModelFromFields(array $fields, Response\Model $model, Response\Model $realModel): array
    {
        foreach ($fields as $field) {
            // if we have a string with only digits, cast to int
            if (isset($field->default) &&
                \is_string($field->default) &&
                \ctype_digit($field->default) &&
                !$this->isIntegerCastingBlackListed($field->id)
                && $field->type !== FlowFieldType::FIELD_TYPE_ENUM
            ) {
                $field->default = $field->default <= \PHP_INT_MAX ? (int)$field->default : $field->default;
            }

            // handle fields with empty id
            if (empty($field->id)) {
                if (isset($field->fields)) {
                    $field->fields = $this->fillModelFromFields($field->fields, $model, $realModel);
                    continue;
                }
                throw new \InvalidArgumentException(\sprintf(
                    'Invalid field, id is empty: %s',
                    \json_encode($field)
                ));
            }

            // we know for sure now we have an id field, let's go!
            $fieldId = $field->id;

            if (isset($field->fields)) {
                $sub = new Response\Model();
                $field->fields = $this->fillModelFromFields($field->fields, $sub, $realModel);
                $model->$fieldId = $sub;
                continue;
            }

            $tempFullModel = clone $realModel;
            $tempFullModel->mergeNewProperties($model);
            $default = $this->defaultValueService->getDefaultValueForField($field, $tempFullModel, false);

            if ($default === 'null') {
                $default = null;
            }

            // for fields without type, there's not much we can do
            if (!isset($field->type)) {
                $model->$fieldId = $default;
                continue;
            }

            // handle base on field type
            switch ($field->type) {
                case 'enum':
                    $model->$fieldId = $default;
                    break;
                case 'upload':
                    $model->$fieldId = [];
                    break;
                case 'hashtagText':
                    if (empty($default)) {
                        $model->$fieldId = [
                            'text' => '',
                            'tags' => [],
                        ];
                    } else {
                        $model->$fieldId = $default;
                    }
                    break;
                case 'date':
                    if (!isset($default)) {
                        $model->$fieldId = null;
                    }

                    $date = null;
                    if ($default === static::DATE_TODAY) {
                        $date = new \DateTime();
                    } elseif (\is_string($default)) {
                        $date = \DateTime::createFromFormat('d-m-Y', $default);

                        if (!$date) {
                            $date = \DateTime::createFromFormat(Format::DB_DATE_FORMAT, $default);
                        }

                        if (!$date) {
                            try {
                                $date = new \DateTime($default);
                            } catch (\Throwable $e) {
                                //not important
                            }
                        }
                    } elseif (\is_array($default)) {
                        // this is used for logging only, when we know where the error is coming from
                        // we can fix it and remove the code below
                        $backtrace = $this->cleanDebugBacktrace();
                        $this->logger->error('The field->default is an array instead of a string '
                            . ' ### ' . \var_export($field, true) . ' ### '
                            . ' ### ' . \var_export($_POST, true) . ' ### '
                            . ' ### ' . \var_export($backtrace, true) . ' ### ');
                    }

                    $model->$fieldId = $date ? $date->format('Y-m-d') : null;
                    break;
                case 'datetime':
                    if (!isset($default)) {
                        $model->$fieldId = null;
                    }

                    $dateTime = null;
                    if (\in_array($default, [static::DATETIME_NOW, static::DATE_TODAY], true)) {
                        $dateTime = new \DateTime();
                        if ($default === static::DATE_TODAY) {
                            $dateTime->setTime(0, 0, 0);
                        }
                    } else {
                        //it seems as if the seconds aren't provided: so try to create the dateTime with seconds
                        //first, and if that fails without seconds
                        $dateTime = \DateTime::createFromFormat('d-m-Y H:i:s', $default);
                        if (!$dateTime) {
                            $dateTime = \DateTime::createFromFormat('d-m-Y H:i', $default);
                        }
                    }

                    $model->$fieldId = $dateTime ? $dateTime->format('Y-m-d H:i:s') : null;
                    break;
                default:
                    $model->$fieldId = $default;
            }
        }

        return $fields;
    }

    private function isIntegerCastingBlackListed(string $fieldId): bool
    {
        if ($fieldId === 'name' || \substr($fieldId, -5) === '|name') {
            return true;
        }

        $blackList = [
            'ean',
            'company_number_c',
            'nace_code_c',
            'paymentterms',
            'postalcode',
            'guarantee_amount_c',
            'number',
            'meter_no_c',
            'phone',
        ];
        foreach ($blackList as $item) {
            if (\strpos($fieldId, $item) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Debug function to cleanup the debug_backtrace
     * This is also used to debug the code, will be cleaned up
     */
    public function cleanDebugBacktrace(): array
    {
        $backtrace = \debug_backtrace();
        foreach ($backtrace as $key => $trace) {
            unset($backtrace[$key]['object'], $backtrace[$key]['type'], $backtrace[$key]['args']);
        }

        return $backtrace;
    }
}
