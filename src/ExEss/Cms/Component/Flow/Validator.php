<?php
namespace ExEss\Cms\Component\Flow;

use Doctrine\Common\Collections\Collection;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use stdClass;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\ParserService;
use ExEss\Cms\Logger\Logger;
use ExEss\Cms\Validators\Exception\ConstraintNotFoundException;
use ExEss\Cms\Validators\Factory\ConstraintFactory;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public const NESTING_LEVEL_ALLOWANCE = 4;

    private ValidatorInterface $validator;

    private ConstraintFactory $constraintFactory;

    private Logger $logger;

    private ParserService $parserService;

    private TranslatorInterface $translator;

    private FlashMessageContainer $flashMessageContainer;

    public function __construct(
        ValidatorInterface $validator,
        ConstraintFactory $constraintFactory,
        Logger $logger,
        ParserService $parserService,
        TranslatorInterface $translator,
        FlashMessageContainer $flashMessageContainer
    ) {
        $this->validator = $validator;
        $this->constraintFactory = $constraintFactory;
        $this->logger = $logger;
        $this->parserService = $parserService;
        $this->translator = $translator;
        $this->flashMessageContainer = $flashMessageContainer;
    }

    public function formatViolations(array $validationErrors): stdClass
    {
        $errors = new stdClass();
        /** @var \Symfony\Component\Validator\ConstraintViolationList $validationError */
        foreach ($validationErrors as $field => $validationError) {
            for ($i = 0; $i < $validationError->count(); $i++) {
                /** @var ConstraintViolation $violation */
                $violation = $validationError->get($i);
                if (!isset($errors->$field)) {
                    $errors->$field = [];
                }
                $errors->$field[] = $this->getMessage($violation);
            }
        }

        return $errors;
    }

    /**
     * @param mixed $model
     */
    public function runValidationRules(Collection $rules, $model, ?string $forFieldName = null): array
    {
        $validationGroupViolations = $this->getConstraintViolations($rules, $model, $forFieldName);

        return $this->getViolations($validationGroupViolations);
    }

    /**
     * @param Collection|\ExEss\Cms\Entity\Validator[] $rules
     * @param mixed $model
     * @throws \InvalidArgumentException In case validators are nested more that 10 levels deep.
     */
    private function getConstraintViolations(
        Collection $rules,
        $model,
        ?string $forFieldName = null,
        int $nestingLevel = 1
    ): array {
        // safety precaution for endless loops
        if ($nestingLevel > self::NESTING_LEVEL_ALLOWANCE) {
            throw new \InvalidArgumentException(\sprintf(
                'Maximum nesting level of %s has been reached, please check the validation config',
                self::NESTING_LEVEL_ALLOWANCE
            ));
        }

        $groupedViolationsValidated = false;

        $validationGroupViolations = [];

        foreach ($rules as $rule) {
            $validationGroup = !empty($group = $rule->getValidationGroup())? $group : 'default';

            try {
                $constraint = $this->constraintFactory->createFromName(
                    $rule->getValidator(),
                    $this->getParams($rule, $model)
                );
            } catch (ConstraintNotFoundException $e) {
                $this->logger->debug($e->getMessage());
                continue;
            } catch (\Exception $e) {
                $this->logger->debug(\sprintf(
                    'Constraint %s was ignored. Error : %s',
                    $rule->getValidator(),
                    $e->getMessage()
                ));
                continue;
            }

            //if a mutator is used, the message of the constraint should be adjusted
            if (!empty($mutator = $rule->getMutator())) {
                $this->changeConstraintMessageByMutator($constraint, $mutator);
            }

            if ($forFieldName !== null && $rule->getField() === '__self__') {
                $fieldName = $forFieldName;
            } else {
                $fieldName =$rule->getField();
            }
            $fieldValue = $this->getFieldValue($model, $fieldName, $mutator);

            if (!isset($validationGroupViolations[$validationGroup])) {
                $groupedViolationsValidated = false;
                $validationGroupViolations[$validationGroup] = [
                    'groupedViolations' => [],
                    'violations' => [],
                    'validated' => 0,
                ];
            }

            if (!empty($conditions = $rule->getChildren())
                && !empty($this->getViolations(
                    $this->getConstraintViolations(
                        $conditions,
                        $model,
                        $fieldName,
                        $nestingLevel + 1
                    )
                ))
            ) {
                continue;
            }

            if ($constraint instanceof Date && $fieldValue instanceof \DateTimeInterface) {
                $constraint = new Type(['type' => \DateTimeInterface::class]);
            }

            if (isset($rule->and_not_null) && (bool) $rule->and_not_null) {
                $constraint = [$constraint];
                $constraint[] = $this->constraintFactory->createFromName('NotNull');
            }

            /** @var \Symfony\Component\Validator\ConstraintViolationList $myViolations */
            $myViolations = $this->validator->validate($fieldValue, $constraint);

            if ($myViolations->has(0)) {
                /** @var ConstraintViolation $violation */
                $violation = $myViolations->get(0);

                $overrideMessage = null;
                if (!empty($rule->custom_error_message)) {
                    $overrideMessage = $this->translator->trans(
                        $rule->custom_error_message,
                        [],
                        TranslationDomain::ERRORS,
                    );
                }

                if ($overrideMessage !== null) {
                    $overrideViolation = new ConstraintViolation(
                        $overrideMessage,
                        $overrideMessage,
                        $violation->getParameters(),
                        $violation->getRoot(),
                        $violation->getPropertyPath(),
                        $violation->getInvalidValue(),
                        $violation->getPlural(),
                        $violation->getCode(),
                        $violation->getConstraint(),
                        $violation->getCause()
                    );
                    $myViolations->set(0, $overrideViolation);
                }

                if (isset($rule->show_on_top) && (bool)$rule->show_on_top) {
                    $this->flashMessageContainer->addFlashMessage(
                        new FlashMessage(
                            $overrideMessage !== '' ? $overrideMessage : $this->getMessage($violation),
                            FlashMessage::TYPE_WARNING
                        )
                    );
                }
            }

            if ($rule->getMode()) {
                if (!$groupedViolationsValidated) {
                    if ($myViolations->count() == 0) {
                        $groupedViolationsValidated = true;
                        $validationGroupViolations[$validationGroup]['groupedViolations'] = [];
                        $validationGroupViolations[$validationGroup]['validated']++;
                    } else {
                        $validationGroupViolations[$validationGroup]['groupedViolations'][$forFieldName] =
                            $myViolations;
                    }
                }
            } elseif ($myViolations->count() > 0) {
                $validationGroupViolations[$validationGroup]['violations'][$forFieldName] = $myViolations;
            } else {
                $validationGroupViolations[$validationGroup]['validated']++;
            }
        }

        return $validationGroupViolations;
    }

    public function getMessage(ConstraintViolation $violation): string
    {
        if ($violation->getPlural() !== null) {
            $messages = \explode('|', $violation->getMessage());
            if ($violation->getPlural() === 1) {
                return $messages[0];
            } else {
                return $messages[1];
            }
        }

        return $violation->getMessage();
    }

    private function getViolations(array $validationGroupViolations): array
    {
        if (empty($validationGroupViolations)) {
            return [];
        }

        foreach ($validationGroupViolations as $key => $validationGroupViolation) {
            if (\count($validationGroupViolation['violations']) == 0
                && \count($validationGroupViolation['groupedViolations']) == 0) {
                return [];
            }
            $validationGroupViolations[$key]['validators'] = \array_merge(
                $validationGroupViolation['violations'],
                $validationGroupViolation['groupedViolations']
            );
        }

        $maxValidated = 0;
        $bestValidators = [];

        foreach ($validationGroupViolations as $validationGroupViolation) {
            if ($validationGroupViolation['validated'] > $maxValidated) {
                $maxValidated = $validationGroupViolation['validated'];
                $bestValidators = $validationGroupViolation['validators'];
            }
        }

        if (empty($bestValidators)) {
            $bestValidators = \array_pop($validationGroupViolations)['validators'];
        }

        return $bestValidators;
    }

    /**
     * @param mixed $model
     */
    private function getParams(\ExEss\Cms\Entity\Validator $rule, $model): array
    {
        $paramArray = [
            'model' => $model,
        ];
        if ($rule->getMin() !== '' && $rule->getMin() !== null) { // do not use empty() because of 0 value
            $paramArray['min'] = $rule->getMin();
        }
        if ($rule->getMax() !== '' && $rule->getMax() !== null) { // do not use empty() because of 0 value
            $paramArray['max'] = $rule->getMax();
        }
        // For the File validator we use the maxfilesize (do not use empty() because of 0 value)
        if ($rule->getValidator() === \ExEss\Cms\Doctrine\Type\Validator::FILE
            && $rule->getMaxFileSize() !== ''
            && $rule->getMaxFileSize() !== null
        ) {
            $paramArray['maxSize'] = $rule->getMaxFileSize();
        }

        if (\in_array(
            $rule->getValidator(),
            [\ExEss\Cms\Doctrine\Type\Validator::EMAIL, \ExEss\Cms\Doctrine\Type\Validator::MULTI_EMAIL],
            true
        )) {
            $paramArray['mode'] = Email::VALIDATION_MODE_HTML5;
        }

        if ($rule->getValue() !== null && $rule->getValue() !== '') {
            if ($rule->getValidator() === \ExEss\Cms\Doctrine\Type\Validator::CHOICE) {
                $paramArray['strict'] = true;
                $paramArray['choices'] = \explode('|', $rule->getValue());
            } elseif ($rule->getValidator() ===  \ExEss\Cms\Doctrine\Type\Validator::REGEX) {
                $paramArray['pattern'] = $rule->getValue();
            } else {
                $value = $rule->getValue();

                /**
                 * The following code is alike the "parseListValues" (ParserService) except this uses the model
                 * and parseListValues uses an entity to replace the value.
                 * Maybe it can be merged into a single piece of code at a certain moment?
                 */
                \preg_match_all('/\%([^%.]*)\%/', $value, $matches);
                foreach ($matches[0] as $match) {
                    $trimmedMatch = \trim($match, '%');
                    $modelValue = $this->getFieldFromModel($model, $trimmedMatch);
                    if (\is_array($modelValue) && !empty($modelValue)) {
                        $modelValue = \array_pop($modelValue);
                    }

                    if (!empty($modelValue)) {
                        if (\is_string($modelValue)) {
                            $value = \str_replace($match, $modelValue, $value);
                        } else {
                            $value = $modelValue;
                        }
                    } else {
                        $value = null;
                    }
                }

                if (\is_string($value) && \preg_match('~^\d{2,4}-\d{2}-\d{2,4}$~', $value)) {
                    $date = new \DateTime($value);
                    if ($date) {
                        $value = $date;
                    }
                }

                $paramArray['value'] = $value;
            }
        }

        if (!empty($rule->getType())) {
            $paramArray['type'] = $rule->getType();
        }

        return $paramArray;
    }

    /**
     * @param mixed $model
     * @return mixed
     */
    private function getFieldFromModel($model, string $field)
    {
        $pattern = '/' . $field . '/';
        $possibleResults =
            \array_intersect_key((array)$model, \array_flip(\preg_grep($pattern, \array_keys((array)$model))));
        foreach ($possibleResults as $key => $possibleResult) {
            if ($key === $field || (\strpos($key, '|' . $field) == true)) {
                return [$key => $possibleResult];
            }
        }

        if (isset($model->{$field})) {
            return $model->{$field};
        } elseif (\method_exists($model, 'get'.\ucfirst($field))) {
            return $model->{'get'.\ucfirst($field)}();
        } else {
            return $this->parserService->parseListValue($model, $field);
        }
    }

    /**
     * @param mixed $model
     * @return \DateTime|null|int
     */
    private function getFieldValue($model, string $fieldName, ?string $mutator = null)
    {
        $fieldValue = '--not--set--';
        if (\is_object($model)) {
            if (($model instanceof Model && $model->offsetExists($fieldName))
                || isset($model->$fieldName)
            ) {
                $fieldValue = $model->$fieldName;
            } elseif (false !== ($dotPos = \strpos($fieldName, '.'))) {
                $base = \substr($fieldName, 0, $dotPos);
                $path = \substr($fieldName, $dotPos + 1);
                $fieldValue = $this->getFieldValue($model->$base, $path);
            } elseif (\method_exists($model, 'get'.\ucfirst($fieldName))) {
                $fieldValue = $model->{'get'.\ucfirst($fieldName)}();
            }
        }
        if ($fieldValue === '--not--set--') {
            $fieldValue = $this->parserService->parseListValue($model, $fieldName);
        }

        // in case of empty string coming in we regard it as null, so certain validators will not fail.
        // a lot of validators consider null not a real value and thus will not do any validation which is
        // what we want. For instance Digit validator should only validate if it are digits when a value is passed
        if ($fieldValue === '') {
            return '';
        }

        if ($fieldValue instanceof Model) {
            return $fieldValue->toArray();
        }

        // in case the value is not a string, there's nothing left to be done
        if (!\is_string($fieldValue)) {
            return $fieldValue;
        }

        //in case the value has the date/dateTime format: determine the format to use to create a dateTime object
        $dateTimeFormat = $this->getDateTimeFormat($fieldValue);

        // unrecognised date or date time format
        if (!isset($dateTimeFormat)) {
            return $fieldValue;
        }

        // the fieldValue matched a date/datetime regex, create a dateTime object of the value so the validator
        // can validate against that object
        $dateTime = \DateTime::createFromFormat($dateTimeFormat, $fieldValue);

        if (!$dateTime) {
            // field value does not seem to be a valid date
            return $fieldValue;
        }

        //if a mutator was set, we should get the mutated value
        if (!empty($mutator)) {
            return $this->getDateTimeMutatedFieldValue($dateTime, $mutator);
        }

        //no mutator has been set: just return the dateTime
        return $dateTime;
    }

    private function getDateTimeFormat(string $value): ?string
    {
        if (\strlen($value) === 10) {
            if (\preg_match('~^\d{4}-\d{2}-\d{2}$~', $value)) {
                $segments = \explode('-', $value);
                if (\checkdate($segments[1], $segments[2], $segments[0])) {
                    return 'Y-m-d';
                }
            } elseif (\preg_match('~^\d{2}-\d{2}-\d{4}$~', $value)) {
                $segments = \explode('-', $value);
                if (\checkdate($segments[1], $segments[0], $segments[2])) {
                    return 'd-m-Y';
                }
            } elseif (\preg_match('~^\d{2}\/\d{2}\/\d{4}$~', $value)) {
                $segments = \explode('/', $value);
                if (\checkdate($segments[1], $segments[0], $segments[2])) {
                    return 'd/m/Y';
                }
            }
        } else {
            if (\preg_match('~^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$~', $value)) {
                return 'Y-m-d H:i:s';
            } elseif (\preg_match('~^\d{2}-\d{2}-\d{4} \d{2}:\d{2}:\d{2}$~', $value)) {
                return 'd-m-Y H:i:s';
            } elseif (\preg_match('~^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}$~', $value)) {
                return 'd/m/Y H:i:s';
            }
        }

        return null;
    }

    /**
     * Get the mutated value from the given dateTime object
     * @return \DateTime|int
     */
    private function getDateTimeMutatedFieldValue(\DateTime $dateTime, ?string $mutator)
    {
        switch (\strtolower($mutator)) {
            case 'day':
                return $dateTime->format('j'); //no leading zero's wanted, so not using 'd'
                break;
            case 'month':
                return $dateTime->format('n'); //no leading zero's wanted, so not using 'm'
                break;
            case 'year':
                return $dateTime->format('Y');
                break;
        }

        //in case a not implemented mutator has been used, just return the original dateTime
        return $dateTime;
    }

    /**
     * Change the message of the Constraint when using a mutator
     */
    private function changeConstraintMessageByMutator(Constraint $constraint, ?string $mutator): void
    {
        //if no mutator is set, no need to change anything
        if (\is_null($mutator) || $mutator === '') {
            return;
        }

        //determine the new message (a part of it)
        $replaceValueMessage = '';
        switch (\strtolower($mutator)) {
            case 'day':
                $replaceValueMessage = 'The day of this value';
                break;
            case 'month':
                $replaceValueMessage = 'The month of this value';
                break;
            case 'year':
                $replaceValueMessage = 'The year of this value';
                break;
        }

        //if a replaceValueMessage has been set, replace the "This value" part in the original message with it
        if ($replaceValueMessage !== '') {
            $constraint->message = \str_replace('This value', $replaceValueMessage, $constraint->message);
        }
    }
}
