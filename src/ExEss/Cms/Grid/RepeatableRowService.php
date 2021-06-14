<?php
namespace ExEss\Cms\Grid;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\FlowStep;
use ExEss\Cms\Entity\Property;
use ExEss\Cms\Dashboard\Model\Grid;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\SaveFlow;
use ExEss\Cms\ListFunctions\HelperClasses\ListHelperFunctions;
use ExEss\Cms\Servicemix\ExternalObjectHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

class RepeatableRowService
{
    private TranslatorInterface $translator;

    private ListHelperFunctions $listHelperFunctions;

    private ExternalObjectHandler $externalObjectHandler;

    private EntityManager $em;

    public function __construct(
        EntityManager $em,
        TranslatorInterface $translator,
        ListHelperFunctions $listHelperFunctions,
        ExternalObjectHandler $externalObjectHandler
    ) {
        $this->translator = $translator;
        $this->listHelperFunctions = $listHelperFunctions;
        $this->externalObjectHandler = $externalObjectHandler;
        $this->em = $em;
    }

    public function processRepeating(Grid $grid, Model $model, Flow $flow, FlowStep $flowStep): void
    {
        foreach ($grid->getColumns() as $column) {
            foreach ($column->getRows() as $row) {
                if ($row->getGrid()) {
                    $this->processRepeating($row->getGrid(), $model, $flow, $flowStep);
                }
                if ($row->getOptions() && $row->getOptions()->getGrid()) {
                    $this->processRepeating($row->getOptions()->getGrid(), $model, $flow, $flowStep);
                }
                if ($row->getType() === Grid\Row::TYPE_EMBEDDED_GUIDANCE) {
                    $this->repeatRowIfNeeded($column, $row, $model, $flow, $flowStep);
                }
            }
        }
    }

    /**
     * @throws \DomainException In case the repeatField has an invalid value.
     */
    private function repeatRowIfNeeded(
        Grid\Column $column,
        Grid\Row $row,
        Model $model,
        Flow $flow,
        FlowStep $flowStep
    ): void {
        if (!($repeatField = $row->getOptions()->getRepeatsBy())) {
            return;
        }

        // $repeatField might be a placeholder defined in the flowStepProperties
        $repeatField = $this->replaceProperty($repeatField, $flowStep);

        $field = $flow->getField($repeatField);

        // Find the original position of the template, so we can add the new ones in the right location
        $rowIndex = \array_search($row, $column->getRows());

        // remove the template
        $column->removeRow($row);

        // check if any values were selected for the repeatField
        if (empty($selectedValues = $this->getSelectedValues($model, $repeatField))) {
            // no value(s) selected, nothing left to do
            return;
        }

        // compose the parent parameters we are going to copy in the child
        $options = $row->getOptions();
        $modelKey = $options->getModelKey();
        if (empty($modelKey)) {
            throw new \DomainException('Incorrectly configured repeatable row, modelKey is not set');
        }
        $parentModelParams = $model->getNamespace($modelKey);
        // do not load the values from this guidance flow
        unset($parentModelParams[$modelKey]);

        // fetch baseFatEntity from parent flow (when editing)
        if (isset($model->baseModule, $model->id) && !empty($model->id)) {
            $baseObject = $this->em->getRepository($model->baseModule)->find($model->id);
        }

        // add a row for each found value
        foreach ($selectedValues as $fieldValue) {
            $rowOptions = (clone $options)
                ->setRecordType('')
                ->setModelId($fieldValue);

            // fetch the selected object
            if ($flow->isExternal() && empty($field->getModule()) && !empty($field->getModuleField())) {
                $repeatsByBean = $model->getFieldValue($field->getModuleField())->getFieldValue($fieldValue);
            } elseif (!empty($field->getModule())) {
                $repeatsByBean = $this->em->getRepository($field->getModule())->find($fieldValue);
            } elseif ($flow->getKey() === SaveFlow::CRUD_RECORD_DETAILS) {
                $repeatsByBean = $fieldValue;
            } else {
                $handlerJson = $field->getEnumValues();
                $handler = null;
                if (!empty($handlerJson) && isset($handlerJson[0]['enumValueSource'])) {
                    $handler = $handlerJson[0]['enumValueSource'];
                }

                if ($handler !== null) {
                    $repeatsByBean = $this->externalObjectHandler->getObject(
                        $handler,
                        ['recordId' => $fieldValue]
                    );
                } else {
                    throw new \DomainException('Repeatable bean cannot be found');
                }
            }

            // replace placeholders with properties from the repeated field
            $guidanceParams = $rowOptions->getGuidanceParams();
            $childModelParams = $guidanceParams['model'] ?? [];
            $repeatsByParams = [];
            foreach ($childModelParams as $paramKey => &$param) {
                if (\strpos($param, '%repeatsBy|') === 0) {
                    $param = $this->listHelperFunctions->parseListValue(
                        $repeatsByBean,
                        \str_replace('%repeatsBy|', '%', $param)
                    );
                    if ($param === "") {
                        $param = null;
                    }
                    $repeatsByParams[] = $paramKey . '="' . $param . '"';
                } elseif (isset($model->$param)) {
                    $param = $model->$param;
                }
            }
            unset($param);

            // try to fetch the recordId (in case of an edit), based on the repeatsBy fields
            $recordId = '';
            if (isset($baseObject)) {
                foreach ($repeatsByParams as $paramValue) {
                    if ($paramValue === 'id="' . $rowOptions->getModelId() . '"') {
                        $recordId = $rowOptions->getModelId();
                    }
                }

                if (empty($recordId)) {
                    // get only the field that contains the repeatable value
                    $fieldsWithModelId = \array_filter(
                        $repeatsByParams,
                        function ($param) use ($rowOptions) {
                            return \strpos($param, $rowOptions->getModelId()) !== false
                                && \strpos($param, '|') === false;
                        }
                    );

                    if (!empty($fieldsWithModelId)) {
                        $magicWhere = '%' . \str_replace(Dwp::PREFIX, '', $modelKey) . '{where:' . \implode(
                            ' AND ',
                            $fieldsWithModelId
                        ) . '}|id%';
                        $recordId = $this->listHelperFunctions->parseListValue(
                            $baseObject,
                            $magicWhere
                        );
                    }
                }
            }
            $rowOptions->setRecordId($recordId);

            // merge it all together
            $guidanceParams['model'] = $parentModelParams + $childModelParams + [
                    Dwp::ROW_OPTIONS_MODEL_KEY => $modelKey,
                    Dwp::ROW_OPTIONS_MODEL_ID => $rowOptions->getModelId(),
                    Dwp::ROW_OPTIONS_REPEATS_BY => $rowOptions->getRepeatsBy(),
                ];
            $rowOptions->setGuidanceParams($guidanceParams);

            $column->addRow((clone $row)->setOptions($rowOptions), $rowIndex++);
        }
    }

    public function getSelectedValues(Model $model, string $repeatField): array
    {
        $repeatValues = $model->findFieldValue($repeatField, []);
        if ($repeatValues instanceof Model) {
            $repeatValues = $repeatValues->toArray();
        }
        if (empty($repeatValues)) {
            return [];
        }

        // make sure we always have an array of values, even if only one value is selected or is selectable
        if (!\is_array($repeatValues)) {
            $repeatValues = [$repeatValues];
        }

        // in case we're looping over a selectWithSearch field, the value looks different
        foreach ($repeatValues as &$repeatValue) {
            if (\is_array($repeatValue) && isset($repeatValue['key'])) {
                $repeatValue = $repeatValue['key'];
            }
        }

        return $repeatValues;
    }

    private function replaceProperty(string $toReplace, FlowStep $flowStep): string
    {
        $toReplace = \str_replace('%', '', $toReplace);

        $found = $flowStep->getProperties()->filter(function (Property $property) use ($toReplace) {
            return $property->getName() === $toReplace;
        });

        /** @var Property $property */
        if ($property = $found->first()) {
            return $this->translator->trans($property->getValue(), [], TranslationDomain::GUIDANCE_GRID);
        }

        return $toReplace;
    }

    /**
     * @return array|Grid\Row[]
     */
    public function getRepeatableRowsIn(Grid $grid): array
    {
        $rows = [];
        foreach ($grid->getColumns() as $column) {
            foreach ($column->getRows() as $row) {
                if ($row->getGrid()) {
                    $rows = \array_merge($rows, $this->getRepeatableRowsIn($row->getGrid()));
                }
                if ($row->getOptions() && $row->getOptions()->getGrid()) {
                    $rows = \array_merge($rows, $this->getRepeatableRowsIn($row->getOptions()->getGrid()));
                }
                if ($row->getType() === Grid\Row::TYPE_EMBEDDED_GUIDANCE && $row->getOptions()->getRepeatsBy()) {
                    $rows[] = $row;
                }
            }
        }

        return $rows;
    }

    public function hasRepeatableRowIn(Grid $grid): bool
    {
        foreach ($grid->getColumns() as $column) {
            foreach ($column->getRows() as $row) {
                if ($row->getGrid()
                    && $this->hasRepeatableRowIn($row->getGrid())
                ) {
                    return true;
                }
                if ($row->getOptions()
                    && $row->getOptions()->getGrid()
                    && $this->hasRepeatableRowIn($row->getOptions()->getGrid())
                ) {
                    return true;
                }
                if ($row->getType() === Grid\Row::TYPE_EMBEDDED_GUIDANCE
                    && $row->getOptions()->getRepeatsBy()
                ) {
                    return true;
                }
            }
        }

        return false;
    }
}
