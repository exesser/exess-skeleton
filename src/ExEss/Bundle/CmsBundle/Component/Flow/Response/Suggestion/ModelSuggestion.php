<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Response\Suggestion;

use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;

class ModelSuggestion implements SuggestionInterface
{
    private string $label;

    private Model $model;

    private ?Model $parentModel;

    private bool $notifyChange = false;

    public function __construct(Model $model, string $label, ?Model $parentModel = null, bool $notifyChange = false)
    {
        $this->model = $model;
        $this->label = $label;
        $this->parentModel = $parentModel;
        $this->notifyChange = $notifyChange;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        $suggestion = [
            'label' => $this->label,
            'model' => $this->model,
            'notifyChange' => $this->notifyChange,
        ];

        if (!empty($this->parentModel)) {
            $suggestion['parentModel'] = $this->parentModel;
        }

        return $suggestion;
    }
}
