<?php
namespace ExEss\Cms\Dashboard;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\Dashboard\Model\Grid;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\FlowStep;
use ExEss\Cms\Entity\GridTemplate;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\Grid\RepeatableRowService;
use ExEss\Cms\ListFunctions\HelperClasses\ListHelperFunctions;
use ExEss\Cms\Parser\ExpressionParserOptions;

class GridRepository
{
    public const DWP_EXP_START = '{%';
    public const DWP_EXP_END = '%}';

    public const TO_TRANSLATE_OPTIONS = [
        'primaryButtonTitle',
        'defaultTitle',
        'titleExpression',
        'title',
        'confirmLabel',
    ];

    private ListHelperFunctions $listHelperFunctions;

    private Security $security;

    private RepeatableRowService $repeatableRowService;

    private EntityManager $em;

    public function __construct(
        EntityManager $em,
        ListHelperFunctions $listHelperFunctions,
        Security $security,
        RepeatableRowService $repeatableRowService
    ) {
        $this->listHelperFunctions = $listHelperFunctions;
        $this->security = $security;
        $this->repeatableRowService = $repeatableRowService;
        $this->em = $em;
    }

    public function getGridByKey(string $gridKey, array $arguments = []): Grid
    {
        /** @var GridTemplate $grid */
        $grid = $this->em->getRepository(GridTemplate::class)->get($gridKey);

        $gridJson = $this->replaceArguments(
            $this->encodeJson(\json_encode($grid->getJsonFields())),
            $this->getAllArguments($arguments)
        );

        return new Grid($gridJson);
    }

    public function getGridForFlowStep(
        FlowStep $flowStep,
        Model $model,
        Flow $flow,
        ?string $recordId = null
    ): Grid {
        $grid = $this->getGridForStep($flowStep);
        $this->repeatableRowService->processRepeating($grid, $model, $flow, $flowStep);

        $grid = $this->replaceFlowStepProperties(
            $grid,
            $flowStep,
            $flow->getBaseObject(),
            $recordId
        );

        // @todo rewrite all other methods to avoid having to json encode and decode again
        // added the rest of the model to be passed to grids containing lists.
        $gridJson = $this->replaceArguments(\json_encode($grid), $this->getAllArguments(
            ['recordId' => $recordId] + $model->toArray()
        ));

        return new Grid($gridJson);
    }

    public function encodeJson(string $json): string
    {
        //Strip /n /r from json.
        return \trim(\preg_replace('/\s+/', ' ', $json));
    }

    public function getAllArguments(array $arguments): array
    {
        if ($this->security instanceof Security) {
            $user = $this->security->getCurrentUser();
            $primaryGroup = $this->security->getPrimaryGroup();

            $arguments['current_user_id'] = $user ? $user->getId() : null;
            $arguments['current_primary_group_id'] = $primaryGroup ? $primaryGroup->getId() : null;
        }

        return $arguments;
    }

    public function replaceArguments(string $json, array $arguments): string
    {
        $json = \str_replace([self::DWP_EXP_START, self::DWP_EXP_END], ['DWP_EXP_START', 'DWP_EXP_END'], $json);

        $json = $this->listHelperFunctions->parseListValue(
            (new ExpressionParserOptions(new Model($arguments)))->setContext(ExpressionParserOptions::CONTEXT_JSON),
            $json
        );

        return \str_replace(['DWP_EXP_START', 'DWP_EXP_END'], [self::DWP_EXP_START, self::DWP_EXP_END], $json);
    }

    /**
     * @throws \InvalidArgumentException In case step has no grid.
     */
    public function getGridForStep(FlowStep $step): Grid
    {
        if (!($gridTemplate = $step->getGridTemplate())) {
            throw new \InvalidArgumentException(\sprintf(
                'Flow step %s is not linked to a grid template',
                $step->getName()
            ));
        }

        $gridJson = '';
        if (!empty($jsonFields = $gridTemplate->getJsonFields())) {
            $gridJson = $this->encodeJson(\json_encode($jsonFields));
        }

        return new Grid($gridJson);
    }

    /**
     * This functions checks if the flow has a repeatable row
     *
     * @throws \DomainException If the flow is missing a linked flow step.
     */
    public function hasFlowRepeatableRows(Flow $flow): bool
    {
        foreach ($flow->getSteps() as $step) {
            if ($this->hasFlowRepeatableRowsInStep($step)) {
                return true;
            }
        }

        return false;
    }

    /**
     * This functions checks if the flow step has a repeatable row
     */
    public function hasFlowRepeatableRowsInStep(FlowStep $flowStep): bool
    {
        $grid = $this->getGridForStep($flowStep);

        return $this->repeatableRowService->hasRepeatableRowIn($grid);
    }

    /**
     * @return array|Grid\Row[]
     * @throws \DomainException If the flow is missing a linked flow step.
     */
    public function getRepeatableRows(Flow $flow): array
    {
        if (!$this->hasFlowRepeatableRows($flow)) {
            return [];
        }

        $rows = [];
        foreach ($flow->getSteps() as $step) {
            $rows[] = $this->getRepeatableRowsInStep($step);
        }

        return \array_merge(...$rows);
    }

    /**
     * @return array|Grid\Row[]
     */
    public function getRepeatableRowsInStep(FlowStep $flowStep): array
    {
        if (!$this->hasFlowRepeatableRowsInStep($flowStep)) {
            return [];
        }

        $grid = $this->getGridForStep($flowStep);

        return $this->repeatableRowService->getRepeatableRowsIn($grid);
    }

    /**
     * @throws \InvalidArgumentException When called for a non repeatable row.
     * @throws \OutOfBoundsException If the repeater value field doesn't contain a Model.
     */
    public function getRepeatValuesFromModel(Grid\Row $row, Model $model): array
    {
        $repeatsByKey = $row->getOptions()->getRepeatsBy();

        if (!$repeatsByKey) {
            throw new \InvalidArgumentException('This method only makes sense for repeatable rows');
        }

        return $this->repeatableRowService->getSelectedValues($model, $repeatsByKey);
    }

    private function replaceFlowStepProperties(
        Grid $grid,
        FlowStep $flowStep,
        ?string $fatEntityName = null,
        ?string $recordId = null
    ): Grid {
        if ($fatEntityName && \class_exists($fatEntityName) && \is_string($recordId)) {
            $baseObject = $this->em->getRepository($fatEntityName)->find($recordId);
        }

        $json = \json_encode($grid);
        foreach ($flowStep->getProperties() as $property) {
            $value = $property->getValue();
            if (isset($baseObject)) {
                $value = $this->listHelperFunctions->parseListValue($baseObject, $value, null);
            }
            $json = \str_replace(
                '%' . $property->getName() . '%',
                $value,
                $json
            );
        }

        return new Grid($json);
    }
}
