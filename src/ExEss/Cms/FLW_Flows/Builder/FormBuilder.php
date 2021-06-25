<?php
namespace ExEss\Cms\FLW_Flows\Builder;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\MappingException;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Doctrine\Type\FlowType;
use ExEss\Cms\Doctrine\Type\GeneratedFieldType;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\FlowStep;
use ExEss\Cms\Helper\DataCleaner;
use ExEss\Cms\Service\SelectWithSearchService;
use stdClass;
use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\Entity\SecurityGroup;
use ExEss\Cms\FLW_Flows\Action\Arguments;
use ExEss\Cms\FLW_Flows\Field;
use ExEss\Cms\FLW_Flows\Response\Form;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\Response\ValidationResult;
use ExEss\Cms\Component\ExpressionParser\ParserService;
use ExEss\Cms\Component\ExpressionParser\Parser\ExpressionParserOptions;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormBuilder
{
    private TranslatorInterface $translator;

    private Field $field;

    private SelectWithSearchService $selectWithSearchService;

    private ParserService $parserService;

    private Security $security;

    private EnumFieldBuilder $enumFieldBuilder;

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        EnumFieldBuilder $enumFieldBuilder,
        ParserService $parserService,
        SelectWithSearchService $selectWithSearchService,
        TranslatorInterface $translator,
        Security $security,
        Field $field
    ) {
        $this->translator = $translator;
        $this->field = $field;
        $this->selectWithSearchService = $selectWithSearchService;
        $this->parserService = $parserService;
        $this->security = $security;
        $this->enumFieldBuilder = $enumFieldBuilder;
        $this->em = $em;
    }

    /**
     * @todo make use of the Form class to set the fields and fieldgroups
     */
    public function setFilledErrorForm(
        Arguments $arguments,
        Flow $flow,
        Model $model,
        ValidationResult $validationResult,
        ?object $baseEntity = null
    ): void {
        $arguments->model = $model;
        $arguments->errors = $validationResult->getErrors();
        $arguments->form = new stdClass();
        $arguments->form->default = new stdClass();
        $fieldGroups = $this->field->getFieldGroupsFromFields($validationResult->getFields());
        $this->enumFieldBuilder->expandFixedEnums($fieldGroups);
        $this->enumFieldBuilder->expandEnums($fieldGroups, $model, $model->baseModule, $baseEntity);
        foreach ($fieldGroups as $fieldGroup) {
            foreach ($fieldGroup as $field) {
                $this->enumFieldBuilder->expandIfConditionalEnum($field, $model);
            }
        }
        // we want to show the fields always
        $fieldsToReturn = [];
        foreach ($fieldGroups as $group => $fields) {
            foreach ($fields as $field) {
                unset($field->hideExpression);
                $fieldsToReturn[] = $field;
            }
        }
        $arguments->form->default->fields = $fieldsToReturn;
        $arguments->grid->getColumns()[0]->getRows()[0]->getOptions()->setText($flow->getErrorMessage());
        $arguments->flowId = $flow->getKey();
    }

    public function getFilledFlowStepForm(
        Flow $flow,
        FlowStep $flowStep,
        Model $model,
        ?object $baseEntity = null,
        ?string $flowAction = null,
        array $params = []
    ): Form {
        $form = new Form(
            $flowStep->getId(),
            $flowStep->getType(),
            $flowStep->getKey(),
            $this->translator->trans($flowStep->getLabel(), [], TranslationDomain::GUIDANCE_TITLE)
        );

        $fieldGroups = $this->field->getFieldGroupsFromFields($flowStep->getFields(), $flowAction);

        //in case of a repeatable block we need to have the recordId from the model inside the params as well so the
        //valueExpressions can be handled
        if (!isset($params['recordId']) && $model->hasNonEmptyValueFor('recordId', true)) {
            $params['recordId'] = $model->recordId;
        }

        $params = $this->enrichParams($fieldGroups, $params);

        // Replace the default values if they have the next structure: '%fieldsKey%' with values from $params
        foreach ($fieldGroups as $fields) {
            $this->fillFieldsBaseOnParams($fields, $params, $baseEntity);
        }

        $this->enumFieldBuilder->expandFixedEnums($fieldGroups); //Non data specific
        $this->enumFieldBuilder->expandEnums($fieldGroups, $model, $flow->getBaseObject(), $baseEntity);

        // Replace the default value with the values of the record from DB
        if ($baseEntity && $flow->getType() === FlowType::STANDARD) {
            foreach ($fieldGroups as $fields) {
                $this->fillFields($fields, $baseEntity);
            }
        }

        // Replace the default value with te value of `field_overwrite_value` if this is not empty
        foreach ($fieldGroups as $fields) {
            $this->replaceDefaultValueWithOverwriteValue($fields, $baseEntity);
        }

        // Convert multi select with search value to and array of arrays - structure requested by DWP
        foreach ($fieldGroups as $fields) {
            foreach ($fields as $field) {
                if ($field->type === 'selectWithSearch') {
                    $this->replaceDefaultValueArrayForSelectWithSearchFields($field);
                }
            }
        }

        foreach ($fieldGroups as $groupName => $fields) {
            $form->setGroup($groupName, $fields);
        }

        return $form;
    }

    public function getFlowStepFields(FlowStep $flowStep): array
    {
        $stepFields = [];
        foreach ($this->field->getFieldGroupsFromFields($flowStep->getFields()) as $groupFields) {
            foreach ($groupFields as $field) {
                $stepFields[] = $field->id;
            }
        }

        return $stepFields;
    }

    private function replaceDefaultValueWithOverwriteValue(
        array $fields,
        ?object $baseEntity = null
    ): void {
        foreach ($fields as $field) {
            if (!empty($field->overwrite_value) || $field->overwrite_value === false) {
                $field->default = $field->overwrite_value;

                if (!\is_null($baseEntity) && !empty($field->default) && \is_string($field->default)) {
                    $field->default = $this->parserService->parseListValue(
                        $baseEntity,
                        $field->default,
                        $field->default
                    );
                }
            }
        }
    }

    private function enrichParams(array $fieldGroups, array $params): array
    {
        $params['current_user_id'] = [
            'all' => $this->security->getCurrentUser()->getId(),
            'selectWithSearch' => [
                [
                    'key' => $this->security->getCurrentUser()->getId(),
                    'label' => $this->security->getCurrentUser()->getName(),
                ],
            ],
        ];

        $primaryGroup = $this->security->getPrimaryGroup();
        if ($primaryGroup instanceof SecurityGroup) {
            $params['current_primary_group_id'] = [
                'all' => $primaryGroup->getId(),
                'selectWithSearch' => [
                    [
                        'key' => $primaryGroup->getId(),
                        'label' => $primaryGroup->getName(),
                    ],
                ],
            ];
        }

        /**
         * Here is where the voodoo starts
         *
         * This is going through all the field groups, and all fields within them, and searches for the first
         * field that ends with |id, and which has a non empty, non boolean default value.
         *
         * This fields prefix before |id becomes the "recordBaseKey" which is used in expressions evaluated in
         * fillFieldsBaseOnParams so it determines the base bean for them.
         *
         * Obviously this completely depends on the order in which the fields were retrieved form the database
         * @see Field::getFieldGroupsFromFields()
         */
        foreach ($fieldGroups as $fields) {
            foreach ($fields as $field) {
                if (
                    $field->default === '%recordId%'
                    && \preg_match('/\|id$/', $field->id)
                    && isset($params['recordType'], $params['recordId'])
                    && !isset($params['recordBean'])
                ) {
                    try {
                        $repository = $this->em->getRepository($params['recordType']);
                    } catch (MappingException $e) {
                        // do nothing, CRENQ depends on this
                        continue;
                    }

                    $params['recordBean'] = $repository->find($params['recordId']);
                    $params['recordBaseKey'] = \substr($field->id, 0, -3);
                    return $params;
                }
            }
        }

        return $params;
    }

    private function fillFieldsBaseOnParams(
        array $fields,
        array $params,
        ?object $baseEntity = null
    ): void {
        foreach ($fields as $field) {
            if (\property_exists($field, 'label')) {
                $field->label = $this->translator->trans($field->label, [], TranslationDomain::GUIDANCE_FIELD);
            }

            if (isset($params['recordBaseKey']) &&
                \strpos($field->id, $params['recordBaseKey']) === 0) {
                $subKey = \str_replace($params['recordBaseKey'] . '|', '', $field->id);
                $parserOptions = (new ExpressionParserOptions($params['recordBean']))
                    ->setContext($field->type)
                ;
                $field->default = $this->parserService->parseListValue(
                    $parserOptions,
                    '%' . $subKey . '%',
                    $field->default
                );
            }
            if (isset($field->valueExpression, $params['recordBaseKey']) &&
                \strpos($field->valueExpression, $params['recordBaseKey']) === 1) {
                $subKey = \str_replace(
                    [$params['recordBaseKey'] . '|', '%'],
                    '',
                    $field->valueExpression
                );
                $field->default = $this->parserService->parseListValue(
                    $params['recordBean'],
                    '%' . $subKey . '%',
                    $field->default
                );
                $field->valueExpression = '';
            }

            if (!\is_null($baseEntity)
                && !empty($field->default)
                && \is_string($field->default)
                && $field->default !== '%recordId%'
            ) {
                $field->default = $this->parserService->parseListValue(
                    $baseEntity,
                    $field->default,
                    $field->default
                );
            }

            foreach ($params as $paramKey => $paramValue) {
                if (!empty($field->default) && !\is_bool($field->default)) {
                    $field->default =
                        $this->replaceValue($field->type, $paramKey, $paramValue, $field->default);
                }

                if (!empty($field->overwrite_value)) {
                    $field->overwrite_value =
                        $this->replaceValue($field->type, $paramKey, $paramValue, $field->overwrite_value);
                }
            }

            // If the value was not found in params
            // and doesn't correspond to the regex then we empty the default value
            // do not use isset .. the value is allowed to be null
            if (!\property_exists($field, 'default') ||
                (\is_string($field->default) && \preg_match('~^%.+%$~', $field->default))
            ) {
                $field->default = '';
            }

            // If the value was not found in params
            // and doesn't correspond to the regex then we empty the overwrite_value
            if (!\property_exists($field, 'overwrite_value') ||
                (\is_string($field->overwrite_value) && \preg_match('~^%.+%$~', $field->overwrite_value))
            ) {
                $field->overwrite_value = '';
            }
        }
    }

    private function fillFields(array $fields, object $baseEntity): void
    {
        foreach ($fields as $field) {
            if (!empty($field->fields)) {
                foreach ($field->fields as $nestedField) {
                    if (empty($nestedField->default)) {
                        $parserOptions = (new ExpressionParserOptions($baseEntity))
                            ->setReplaceEnumValueWithLabel(
                                $field->type === FlowFieldType::FIELD_TYPE_LABEL_AND_TEXT
                            )
                        ;
                        $nestedField->default = $this->parserService->parseListValue(
                            $parserOptions,
                            '%' . $field->id . '|' . $nestedField->id . '%'
                        );
                    }
                }
            } elseif (($field->generateType ?? '') === GeneratedFieldType::REPEAT_TRIGGER) {
                $parserOptions = (new ExpressionParserOptions($baseEntity))
                    ->setContext($field->type)
                ;
                $field->moduleField = $field->moduleField ?? null;
                $dbValue = $this->parserService->parseListValue(
                    $parserOptions,
                    $field->moduleField,
                    $field->default
                );
                $field->default = $dbValue;
            } else {
                if (\substr($field->id, 0, 4) !== Dwp::PREFIX
                    && $field->id !== 'recordTypeOfRecordId'
                ) {
                    $parserOptions = (new ExpressionParserOptions($baseEntity))
                        ->setReplaceEnumValueWithLabel(true)
                        ->setContext($field->type)
                    ;
                    $dbValue = $this->parserService->parseListValue(
                        $parserOptions,
                        '%' . $field->id . '%',
                        $field->default
                    );
                    // in case the DB was empty the default value should not be overwritten.
                    // This can happen in case of for instance a default of NOW with a database value of ''
                    // in that case we still need to have the default of NOW.
                    if ($dbValue !== '') {
                        $field->default = $dbValue;
                    }
                }
            }

            if ($field->type === FlowFieldType::FIELD_TYPE_LABEL_AND_ACTION) {
                if (!empty($field->action) && !empty($field->action['params'])) {
                    foreach ($field->action['params'] as $actionParamKey => $actionParam) {
                        if (!\is_string($actionParam) || \substr($field->id, 0, 4) === Dwp::PREFIX) {
                            continue;
                        }
                        $field->action['params'][$actionParamKey] = $this->parserService->parseListValue(
                            $baseEntity,
                            $actionParam,
                            $actionParam
                        );
                    }
                }
            }
        }
    }

    private function replaceDefaultValueArrayForSelectWithSearchFields(stdClass $field): void
    {
        if ($field->default == '' || $field->default == []) {
            $field->default = [];
            return;
        }

        if (\is_array($field->default) && isset($field->default[0]['key'], $field->default[0]['label'])) {
            return;
        }

        $field->default = $this->selectWithSearchService->getLabelsForValues(
            $field->datasourceName,
            \is_string($field->default) ? [$field->default] : $field->default
        );
    }

    /**
     * @param string|array $field
     * @param string|array $paramValue
     * @return string|array
     */
    private function replaceValue(string $fieldType, string $paramKey, $paramValue, $field)
    {
        if (\in_array($paramKey, ['current_user_id', 'current_primary_group_id', 'current_dealer_id'], true)) {
            if (('%'.$paramKey.'%') !== $field) {
                return $field;
            }

            if ($fieldType === 'selectWithSearch') {
                $paramValue = $paramValue['selectWithSearch'];
            } else {
                $paramValue = $paramValue['all'];
            }
        }

        if (\is_array($paramValue) && \in_array($field, [$paramKey, '%' . $paramKey . '%'], true)) {
            return $paramValue;
        }

        if (!\is_string($paramValue) || !\is_string($field)) {
            return $field;
        }

        return \str_replace('%' . $paramKey . '%', $paramValue, $field);
    }

    /**
     * This method unfortunatly needs the complete model, so we'll call it later after we populated the model
     */
    public function expandLabelAndActionFromModel(Form $form, Model $model): void
    {
        foreach ($form->getGroups() as $group) {
            foreach ($group->fields as $field) {
                if (($field->type === FlowFieldType::FIELD_TYPE_LABEL_AND_ACTION) && !empty($field->action)) {
                    $actionJson = \json_encode($field->action);
                    foreach ($model as $key => $value) {
                        if (\is_string($value) || \is_numeric($value)) {
                            $actionJson = \str_replace('%' . $key . '%', $value, $actionJson);
                        }
                    }
                    $field->action = DataCleaner::jsonDecode($actionJson);
                }
            }
        }
    }
}
