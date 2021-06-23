<?php
namespace ExEss\Cms\Dashboard\Model\Grid\Row;

use ExEss\Cms\Dashboard\Model\Grid;
use ExEss\Cms\Dashboard\Model\StripEmptyOnEncodeTrait;

class Options implements \JsonSerializable
{
    use StripEmptyOnEncodeTrait;

    private ?string $id = null;

    private ?string $recordType = null;

    private ?string $dashboardName = null;

    private ?string $flowAction = null;

    private ?string $flowId = null;

    private ?string $recordId = null;

    private ?bool $showPrimaryButton = null;

    private ?string $primaryButtonTitle = null;

    private ?string $defaultTitle = null;

    private ?string $titleExpression = null;

    private ?string $title = null;

    private ?string $formKey = null;

    private ?string $listKey = null;

    private ?string $label = null;

    private ?string $icon = null;

    private ?string $amount = null;

    private ?string $lines = null;

    private ?string $hasWarning = null;

    private ?string $showCheckmark = null;

    private ?string $line = null;

    private ?string $boldLine = null;

    private ?string $buttons = null;

    private ?string $text = null;

    private ?string $src = null;

    private ?string $gridKey = null;

    private string $repeatsBy;

    private string $modelKey;

    private array $actionData = [];

    private string $modelId;

    private ?Grid $grid = null;

    private array $guidanceParams = [];

    private array $params = [];

    private ?Option\Action $action = null;

    private array $items = [];

    /**
     * @throws \InvalidArgumentException In case the argument contains unsupported options.
     */
    public function __construct(array $source)
    {
        $stringProperties = [
            'id',
            'recordType',
            'dashboardName',
            'flowAction',
            'flowId',
            'recordId',
            'primaryButtonTitle',
            'defaultTitle',
            'titleExpression',
            'title',
            'formKey',
            'listKey',
            'label',
            'icon',
            'amount',
            'lines',
            'hasWarning',
            'showCheckmark',
            'line',
            'boldLine',
            'buttons',
            'text',
            'src',
            'repeatsBy',
            'modelKey',
            'modelId',
            'gridKey',
            'actionData',
            'params',
        ];
        foreach ($stringProperties as $property) {
            if (($value = $source[$property] ?? false) !== false) {
                $setter = 'set' . \ucfirst($property);
                $this->$setter($value);
                unset($source[$property]);
            }
        }
        $showPrimaryButton = $source['showPrimaryButton'] ?? null;
        if ($showPrimaryButton !== null) {
            $this->setShowPrimaryButton($showPrimaryButton);
            unset($source['showPrimaryButton']);
        }
        if ($grid = $source['grid'] ?? false) {
            $this->setGrid(new Grid($grid));
            unset($source['grid']);
        }
        if ($guidanceParams = $source['guidanceParams'] ?? false) {
            $this->setGuidanceParams($guidanceParams);
            unset($source['guidanceParams']);
        }
        if ($params = $source['params'] ?? false) {
            $this->setParams($params);
            unset($source['params']);
        }
        if (($items = $source['items'] ?? false) && \is_array($items)) {
            foreach ($items as $item) {
                $this->addItem(new Option\Item($item));
            }
            unset($source['items']);
        }
        if ($action = $source['action'] ?? false) {
            $this->setAction(new Option\Action($action));
            unset($source['action']);
        }

        if (\count($source)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unsupported option: %s',
                \implode(', ', \array_keys($source))
            ));
        }
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): Options
    {
        $this->id = $id;

        return $this;
    }

    public function getRecordType(): ?string
    {
        return $this->recordType;
    }

    public function setRecordType(string $recordType): Options
    {
        $this->recordType = $recordType;

        return $this;
    }

    public function getDashboardName(): ?string
    {
        return $this->dashboardName;
    }

    public function setDashboardName(string $dashboardName): Options
    {
        $this->dashboardName = $dashboardName;

        return $this;
    }

    public function getFlowAction(): ?string
    {
        return $this->flowAction;
    }

    public function setFlowAction(string $flowAction): Options
    {
        $this->flowAction = $flowAction;

        return $this;
    }

    public function getFlowId(): ?string
    {
        return $this->flowId;
    }

    public function setFlowId(string $flowId): Options
    {
        $this->flowId = $flowId;

        return $this;
    }

    /**
     * @return array
     */
    public function getActionData(): array
    {
        return $this->actionData;
    }

    /**
     * @param string|array $actionData
     */
    public function setActionData($actionData): void
    {
        if (\is_string($actionData)) {
            $actionData = \json_decode($actionData, true);
        }

        $this->actionData = $actionData ?? [];
    }

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function setRecordId(string $recordId): Options
    {
        $this->recordId = $recordId;

        return $this;
    }

    public function getShowPrimaryButton(): ?bool
    {
        return $this->showPrimaryButton;
    }

    public function setShowPrimaryButton(bool $showPrimaryButton): Options
    {
        $this->showPrimaryButton = $showPrimaryButton;

        return $this;
    }

    public function getPrimaryButtonTitle(): ?string
    {
        return $this->primaryButtonTitle;
    }

    public function setPrimaryButtonTitle(string $primaryButtonTitle): Options
    {
        $this->primaryButtonTitle = $primaryButtonTitle;

        return $this;
    }

    public function getDefaultTitle(): ?string
    {
        return $this->defaultTitle;
    }

    public function setDefaultTitle(string $defaultTitle): Options
    {
        $this->defaultTitle = $defaultTitle;

        return $this;
    }

    public function getTitleExpression(): ?string
    {
        return $this->titleExpression;
    }

    public function setTitleExpression(string $titleExpression): Options
    {
        $this->titleExpression = $titleExpression;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): Options
    {
        $this->title = $title;

        return $this;
    }

    public function getFormKey(): ?string
    {
        return $this->formKey;
    }

    public function setFormKey(string $formKey): Options
    {
        $this->formKey = $formKey;

        return $this;
    }

    public function getListKey(): ?string
    {
        return $this->listKey;
    }

    public function setListKey(string $listKey): Options
    {
        $this->listKey = $listKey;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): Options
    {
        $this->label = $label;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): Options
    {
        $this->icon = $icon;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): Options
    {
        $this->amount = $amount;

        return $this;
    }

    public function getLines(): ?string
    {
        return $this->lines;
    }

    public function setLines(string $lines): Options
    {
        $this->lines = $lines;

        return $this;
    }

    public function getHasWarning(): ?string
    {
        return $this->hasWarning;
    }

    public function setHasWarning(string $hasWarning): Options
    {
        $this->hasWarning = $hasWarning;

        return $this;
    }

    public function getShowCheckmark(): ?string
    {
        return $this->showCheckmark;
    }

    public function setShowCheckmark(string $showCheckmark): Options
    {
        $this->showCheckmark = $showCheckmark;

        return $this;
    }

    public function getLine(): ?string
    {
        return $this->line;
    }

    public function setLine(string $line): Options
    {
        $this->line = $line;

        return $this;
    }

    public function getBoldLine(): ?string
    {
        return $this->boldLine;
    }

    public function setBoldLine(string $boldLine): Options
    {
        $this->boldLine = $boldLine;

        return $this;
    }

    public function getButtons(): ?string
    {
        return $this->buttons;
    }

    public function setButtons(string $buttons): Options
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function setSrc(string $src): Options
    {
        $this->src = $src;

        return $this;
    }

    public function setText(string $text): Options
    {
        $this->text = $text;

        return $this;
    }

    public function getRepeatsBy(): ?string
    {
        return $this->repeatsBy;
    }

    public function setRepeatsBy(string $repeatsBy): Options
    {
        $this->repeatsBy = $repeatsBy;

        return $this;
    }

    public function getModelKey(): ?string
    {
        return $this->modelKey;
    }

    public function setModelKey(string $modelKey): Options
    {
        $this->modelKey = $modelKey;

        return $this;
    }

    public function getModelId(): ?string
    {
        return $this->modelId;
    }

    public function setModelId(string $modelId): Options
    {
        $this->modelId = $modelId;

        return $this;
    }

    public function getGridKey(): ?string
    {
        return $this->modelId;
    }

    public function setGridKey(string $gridKey): Options
    {
        $this->gridKey = $gridKey;

        return $this;
    }

    public function getGrid(): ?Grid
    {
        return $this->grid;
    }

    public function setGrid(Grid $grid): Options
    {
        $this->grid = $grid;

        return $this;
    }

    public function getGuidanceParams(): array
    {
        return $this->guidanceParams;
    }

    public function setGuidanceParams(array $guidanceParams): Options
    {
        $this->guidanceParams = $guidanceParams;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): Options
    {
        $this->params = $params;

        return $this;
    }

    public function getAction(): ?Option\Action
    {
        return $this->action;
    }

    public function setAction(Option\Action $action): Options
    {
        $this->action = $action;

        return $this;
    }

    public function addItem(Option\Item $item): Options
    {
        $this->items[] = $item;

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
