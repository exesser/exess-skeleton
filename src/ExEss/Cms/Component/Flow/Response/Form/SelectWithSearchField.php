<?php

namespace ExEss\Cms\Component\Flow\Response\Form;

class SelectWithSearchField extends Field
{
    public const TYPE = 'selectWithSearch';

    public string $datasourceName;

    public string $modalTitle = "Select one or more records";

    public string $selectedResultsTitle = "Selected records";

    public string $plusButtonTitle = "Select one or more records";

    public string $addResultsButtonTitle = "Add one or more records";

    public bool $multiple = false;

    public array $params = [];

    public function __construct(string $id, string $label, string $datasourceName)
    {
        parent::__construct($id, $label, static::TYPE);
        $this->setDatasourceName($datasourceName);
    }

    public function setDatasourceName(string $datasourceName): void
    {
        $this->datasourceName = $datasourceName;
    }

    public function setModalTitle(string $modalTitle): void
    {
        $this->modalTitle = $modalTitle;
    }

    public function setSelectedResultsTitle(string $selectedResultsTitle): void
    {
        $this->selectedResultsTitle = $selectedResultsTitle;
    }

    public function setPlusButtonTitle(string $plusButtonTitle): void
    {
        $this->plusButtonTitle = $plusButtonTitle;
    }

    public function setAddResultsButtonTitle(string $addResultsButtonTitle): void
    {
        $this->addResultsButtonTitle = $addResultsButtonTitle;
    }

    public function setMultiple(bool $multiple): void
    {
        $this->multiple = $multiple;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getDatasourceName(): string
    {
        return $this->datasourceName;
    }
}
