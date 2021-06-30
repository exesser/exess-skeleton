<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow;

use stdClass;
use ExEss\Bundle\CmsBundle\Collection\ObjectCollection;
use ExEss\Bundle\CmsBundle\Dashboard\Model\Grid;
use ExEss\Bundle\CmsBundle\Component\Flow\Action\Command;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\CurrentStep;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Form;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Suggestions;

class Response implements \JsonSerializable
{
    /**
     * @todo create domain model for errors
     */
    private stdClass $errors;

    private Suggestions $suggestions;

    private ?Guidance $guidance = null;

    private ?Form $form = null;

    private ?Grid $grid = null;

    private Model $model;

    private ?Model $parentModel = null;

    private ?ObjectCollection $steps = null;

    private ?CurrentStep $currentStep = null;

    private ?Command $command = null;

    private bool $forceReload = false;

    public function __construct()
    {
        $this->errors = new stdClass();
        $this->suggestions = new Suggestions();
        $this->model = new Model();
    }

    /**
     * @return $this
     */
    public function setFromOther(Response $response)
    {
        $this->errors = $response->getErrors();
        $this->suggestions = $response->getSuggestions();
        $this->guidance = $response->getGuidance();
        $this->form = $response->getForm();
        $this->grid = $response->getGrid();
        $this->model = $response->getModel();
        $this->steps = $response->getSteps();
        $this->currentStep = $response->getCurrentStep();
        $this->command = $response->getCommand();

        return $this;
    }

    public function getErrors(): stdClass
    {
        return $this->errors;
    }

    public function setErrors(stdClass $errors): Response
    {
        $this->errors = $errors;

        return $this;
    }

    public function getSuggestions(): Suggestions
    {
        return $this->suggestions;
    }

    public function setSuggestions(Suggestions $suggestions): Response
    {
        $this->suggestions = $suggestions;

        return $this;
    }

    public function getGuidance(): ?Guidance
    {
        return $this->guidance;
    }

    public function setGuidance(?Guidance $guidance = null): Response
    {
        $this->guidance = $guidance;

        return $this;
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setForm(?Form $form = null): Response
    {
        $this->form = $form;

        return $this;
    }

    public function getGrid(): ?Grid
    {
        return $this->grid;
    }

    public function setGrid(?Grid $grid = null): Response
    {
        $this->grid = $grid;

        return $this;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function setModel(Model $model): Response
    {
        $this->model = $model;

        return $this;
    }

    public function getSteps(): ?ObjectCollection
    {
        return $this->steps;
    }

    public function setSteps(?ObjectCollection $steps = null): Response
    {
        $this->steps = $steps;

        return $this;
    }

    public function getCurrentStep(): ?CurrentStep
    {
        return $this->currentStep;
    }

    public function setCurrentStep(CurrentStep $currentStep): Response
    {
        $this->currentStep = $currentStep;

        return $this;
    }

    public function getCommand(): ?Command
    {
        return $this->command;
    }

    public function setCommand(Command $command): Response
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): stdClass
    {
        // return all properties that are not null
        $serialised = [];

        foreach (\get_object_vars($this) as $property => $value) {
            if ($value === null
                || ((\is_array($value) || $value instanceof stdClass) && empty((array)$value))
                || ($value instanceof \Countable && !$value->count())
            ) {
                continue;
            }
            $serialised[$property] = $value;
        }

        // stay backward compatible
        if (isset($serialised['steps'])) {
            $serialised['progress'] = [
                'steps' => $serialised['steps'],
            ];
            unset($serialised['steps']);
        } else {
            // never return currentStep if steps is not set
            unset($serialised['currentStep']);
        }

        if (isset($serialised['forceReload'])) {
            unset($serialised['forceReload']);
        }

        if (isset($serialised['currentStep'])) {
            $serialised['step'] = $serialised['currentStep'];
            unset($serialised['currentStep']);
        }
        if (isset($serialised['command'])) {
            $serialised['processCommand'] = true;
        }
        if (empty((array) $serialised['suggestions']->jsonSerialize())) {
            unset($serialised['suggestions']);
        }

        return (object) $serialised;
    }

    public function setForceReload(bool $forceReload): void
    {
        $this->forceReload = $forceReload;
    }

    public function isForceReload(): bool
    {
        return $this->forceReload;
    }

    public function getParentModel(): ?Model
    {
        return $this->parentModel;
    }

    public function setParentModel(Model $parentModel): Response
    {
        $this->parentModel = $parentModel;

        return $this;
    }
}
