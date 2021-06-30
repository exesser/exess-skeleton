<?php

namespace ExEss\Bundle\CmsBundle\Component\Flow;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use ExEss\Bundle\CmsBundle\Entity\Flow;
use ExEss\Bundle\CmsBundle\Entity\FlowField;
use ExEss\Bundle\CmsBundle\Entity\FlowStep;
use ExEss\Bundle\CmsBundle\Service\GridService;
use stdClass;
use ExEss\Bundle\CmsBundle\Dictionary\Model\Dwp;

class FlowValidator
{
    private Validator $validator;

    private GridService $gridService;

    private EntityManager $em;

    public function __construct(
        EntityManager $em,
        Validator $validator,
        GridService $gridService
    ) {
        $this->validator = $validator;
        $this->gridService = $gridService;
        $this->em = $em;
    }

    public function validateFlow(Flow $flow, Response\Model $model): Response\ValidationResult
    {
        $result = new Response\ValidationResult();

        foreach ($flow->getSteps() as $flowStep) {
            $this->validateFlowStep($flowStep, $model, $result);
        }

        return $result;
    }

    /**
     * @throws \OutOfBoundsException In case the repeat values contain an invalid value.
     */
    public function validateFlowStep(
        FlowStep $flowStep,
        Response\Model $model,
        Response\ValidationResult $result
    ): void {
        foreach ($flowStep->getFields() as $field) {
            $validationResult = $this->validateField($flowStep, $model, $field->getFieldId(), true);

            if (empty((array)$validationResult)) {
                continue;
            }
            foreach ((array)$validationResult as $key => $error) {
                $errors = $result->getErrors();
                $errors[$key] = \array_unique($error);
                $result->setErrors($errors);
            }

            $fields = $result->getFields();
            $fields[] = $field;

            $result->setFields($fields);
            $result->setValid(false);
        }

        // validate all child flows in this flow step
        $repeatableRows = $this->gridService->getRepeatableRowsInStep($flowStep);
        /** @var \ExEss\Bundle\CmsBundle\Dashboard\Model\Grid\Row $repeatableRow */
        foreach ($repeatableRows as $repeatableRow) {
            $modelKey = $repeatableRow->getOptions()->getModelKey();
            $repeatedFlow = $this->em->getRepository(Flow::class)->get($repeatableRow->getOptions()->getFlowId());

            $childModels = $model->$modelKey ?? new Response\Model();
            foreach ($this->gridService->getRepeatValuesFromModel($repeatableRow, $model) as $repeatKey) {
                if ($childModels->$repeatKey instanceof Response\Model) {
                    $childModel = clone $childModels->$repeatKey;
                } else {
                    $childModel = new Response\Model();
                }
                $childModel->setFieldValue(Dwp::PARENT_MODEL, $model);

                $childValidationResult = $this->validateFlow($repeatedFlow, $childModel);

                if (!$childValidationResult->isValid()) {
                    $result->setValid(false);
                    $errors = $result->getErrors();
                    if (!isset($errors[$modelKey])) {
                        $errors[$modelKey] = [];
                    }
                    $errors[$modelKey][$repeatKey] = $childValidationResult->getErrors();
                    $result->setErrors($errors);
                }
            }
        }
    }

    public function validateField(
        FlowStep $step,
        Response\Model $model,
        string $fieldName,
        bool $excludeFileValidators = false
    ): stdClass {
        $validationErrors = $this->validator->runValidationRules(
            $this->getValidationRules($step, $fieldName, $excludeFileValidators),
            $model,
            $fieldName
        );

        return $this->validator->formatViolations($validationErrors);
    }

    private function getValidationRules(
        FlowStep $flowStep,
        string $fieldName,
        bool $excludeFileValidators
    ): Collection {
        $found = $flowStep->getFields()->filter(function (FlowField $field) use ($fieldName) {
            return $field->getFieldId() === $fieldName;
        });
        /** @var FlowField $stepField */
        $stepField = $found->current();
        if (empty($stepField)) {
            // Do not throw an error here, this is an allowed situation (though a bit bizar)
            return new ArrayCollection();
        }

        $validators = $stepField->getValidators();

        if ($excludeFileValidators) {
            return $validators->filter(
                function (\ExEss\Bundle\CmsBundle\Entity\Validator $validator) {
                    return $validator->getValidator() !== \ExEss\Bundle\CmsBundle\Doctrine\Type\Validator::FILE;
                }
            );
        }

        return $validators;
    }
}
