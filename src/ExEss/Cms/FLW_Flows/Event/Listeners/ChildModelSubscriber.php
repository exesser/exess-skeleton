<?php
namespace ExEss\Cms\FLW_Flows\Event\Listeners;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Events\FlowEvents;
use ExEss\Cms\Dashboard\GridRepository;
use ExEss\Cms\Dashboard\Model\Grid;
use ExEss\Cms\Dashboard\Model\Grid\RowsCollection;
use ExEss\Cms\FLW_Flows\Event\FlowEventDispatcher;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChildModelSubscriber implements EventSubscriberInterface
{

    private GridRepository $gridRepository;

    private FlowEventDispatcher $flowEventDispatcher;

    public function __construct(
        GridRepository $gridRepository,
        FlowEventDispatcher $flowEventDispatcher
    ) {
        $this->gridRepository = $gridRepository;
        $this->flowEventDispatcher = $flowEventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FlowEvents::INIT => [
                ['loadChildModel', -10]
            ]
        ];
    }

    /**
     * Load model for repeatable blocks (only for multi step flows)
     *
     * @throws \LogicException If FormAndModelSubscriber was not run yet.
     */
    public function loadChildModel(FlowEvent $event): void
    {
        if (!$event->getResponse()->getForm()) {
            throw new \LogicException('ChildModelSubscriber must run after FormAndModelSubscriber');
        }

        if ($event->getFlow()->getSteps()->count() < 2) {
            return;
        }

        $allRepeatableBlocks = $this->getFlowRepeatableBlocks($event);

        if ($allRepeatableBlocks->count() < 1) {
            return;
        }

        /** @var Grid\Row $repeatableBlock */
        foreach ($allRepeatableBlocks as $repeatableBlock) {
            $repeatableBlockOptions = $repeatableBlock->getOptions();
            $modelId = $repeatableBlockOptions->getModelId();
            $modelKey = $repeatableBlockOptions->getModelKey();
            $recordId = $repeatableBlockOptions->getRecordId();
            $recordType = $repeatableBlockOptions->getRecordType();
            $initialModel = new Response\Model($repeatableBlockOptions->getGuidanceParams()['model'] ?? []);
            $model = $event->getModel();

            if (!$model->offsetExists($modelKey)) {
                $model->offsetSet($modelKey, new Response\Model());
            }

            if (!$model->$modelKey->offsetExists($modelId)) {
                $model->$modelKey->offsetSet($modelId, new Response\Model());
            }

            $childModel = $model->$modelKey->$modelId;
            $childModel->merge($initialModel);

            $flowActionData = ['event' => FlowAction::EVENT_INIT_CHILD_FLOW];
            if (!empty($recordId)) {
                $flowActionData['recordIds'] = [$recordId];
            }

            // this will automatic populate the model
            $this->flowEventDispatcher->dispatch(
                $repeatableBlockOptions->getFlowId(),
                new FlowAction($flowActionData),
                $childModel,
                $model,
                [],
                !empty($recordType) ? $recordType : null
            );
        }
    }

    private function getFlowRepeatableBlocks(FlowEvent $event): RowsCollection
    {
        $allRepeatableRows = new RowsCollection();

        foreach ($event->getFlow()->getSteps() as $flowStep) {
            $grid = $this->gridRepository->getGridForFlowStep(
                $flowStep,
                $event->getModel(),
                $event->getFlow(),
                $event->getRecordId()
            );
            $this->getRepeatableBlocksFromGrid($grid, $allRepeatableRows);
        }

        return $allRepeatableRows;
    }

    public function getRepeatableBlocksFromGrid(Grid $grid, RowsCollection $allRepeatableRows): void
    {
        foreach ($grid->getColumns() as $column) {
            foreach ($column->getRows() as $row) {
                if ($row->getGrid()) {
                    $this->getRepeatableBlocksFromGrid($row->getGrid(), $allRepeatableRows);
                }

                if ($row->getOptions() && $row->getOptions()->getGrid()) {
                    $this->getRepeatableBlocksFromGrid($row->getOptions()->getGrid(), $allRepeatableRows);
                }

                if ($row->getType() === Grid\Row::TYPE_EMBEDDED_GUIDANCE) {
                    $allRepeatableRows[] = $row;
                }
            }
        }
    }
}
