<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\Entity\DashboardMenuAction;
use ExEss\Bundle\CmsBundle\Entity\DashboardMenuActionGroup;
use ExEss\Bundle\CmsBundle\Entity\ListRowAction;
use ExEss\Bundle\CmsBundle\Entity\ListTopAction;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\ParserService;
use ExEss\Bundle\CmsBundle\Logger\Logger;
use ExEss\Bundle\CmsBundle\Validators\Exception\ConstraintNotFoundException;
use ExEss\Bundle\CmsBundle\Validators\Factory\ConstraintFactory;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ActionService
{
    private const CONDITION_ENABLED = 'enableConditions';
    private const CONDITION_HIDDEN = 'hideConditions';

    private ParserService $parserService;

    private ValidatorInterface $validatorInterface;

    private Logger $logger;

    private ConstraintFactory $constraintFactory;

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        ParserService $parserService,
        ValidatorInterface $validatorInterface,
        ConstraintFactory $constraintFactory,
        Logger $logger
    ) {
        $this->parserService = $parserService;
        $this->validatorInterface = $validatorInterface;
        $this->logger = $logger;
        $this->constraintFactory = $constraintFactory;
        $this->em = $em;
    }

    /**
     * @param ListTopAction|DashboardMenuAction|ListRowAction $action
     */
    public function isEnabled(object $action, object $entity): bool
    {
        return $this->checkConditions($action, $entity, self::CONDITION_ENABLED);
    }

    /**
     * @param ListTopAction|DashboardMenuAction|ListRowAction|DashboardMenuActionGroup $action
     */
    public function isHidden(object $action, object $entity): bool
    {
        return $this->checkConditions($action, $entity, self::CONDITION_HIDDEN, false);
    }

    /**
     * @param ListTopAction|DashboardMenuAction|ListRowAction|DashboardMenuActionGroup $action
     */
    private function checkConditions(
        object $action,
        object $entity,
        string $property,
        bool $default = true
    ): bool {
        if (!$this->em->getClassMetadata(\get_class($action))->hasField($property)) {
            throw new \InvalidArgumentException('Invalid property $property for ' . \get_class($action));
        }

        /**
         * @see ListTopAction::getEnableConditions()
         * @see ListTopAction::getHideConditions()
         * @see DashboardMenuAction::getEnableConditions()
         * @see DashboardMenuAction::getHideConditions()
         * @see ListRowAction::getEnableConditions()
         * @see ListRowAction::getHideConditions()
         * @see DashboardMenuActionGroup::getHideConditions()
         */
        if ($property === self::CONDITION_ENABLED) {
            $condition = $action->getEnableConditions();
        } elseif ($property === self::CONDITION_HIDDEN) {
            $condition = $action->getHideConditions();
        } else {
            throw new \InvalidArgumentException("Invalid property $property");
        }

        if (empty($condition)) {
            return $default;
        }

        return $this->validateCondition($action, $condition, $entity);
    }

    /**
     * @param ListTopAction|DashboardMenuAction|ListRowAction|DashboardMenuActionGroup $action
     */
    private function validateCondition(object $action, array $parentCondition, object $entity): bool
    {
        if (!isset($parentCondition['operator']) || !isset($parentCondition['conditions'])) {
            $this->logIncorrectStructure($action);
            return true;
        }

        $results = [];
        foreach ($parentCondition['conditions'] as $condition) {
            if (isset($condition['field'])) {
                $results[] = $this->validateFieldCondition($condition, $entity);
            } else {
                $results[] = $this->validateCondition($action, $condition, $entity);
            }
        }

        if (\strtolower($parentCondition['operator']) === 'and') {
            return !\in_array(false, $results, true);
        }

        return \in_array(true, $results, true);
    }

    private function validateFieldCondition(array $condition, object $entity): bool
    {
        $condition['params'] = $condition['params'] ?? [];
        foreach ($condition['params'] as &$param) {
            $param = $this->replaceParamsMarkersWithBeanValues($entity, $param);
        }
        unset($param);

        $constraints = [];
        if (!empty($condition['validator'])) {
            try {
                $constraints[] = $this->constraintFactory->createFromName(
                    $condition['validator'],
                    $condition['params']
                );
            } catch (ConstraintNotFoundException $e) {
                $this->logger->error($e->getMessage());
            } catch (\Throwable $e) {
                $this->logger->error(\sprintf(
                    'Constraint %s was ignored. Error : %s',
                    $condition['validator'],
                    $e->getMessage()
                ));
            }
        }

        $fieldValue = $this->parserService->parseListValue($entity, $condition['field'], null);
        $fieldValue = $this->checkForDateValue($fieldValue);

        if ($fieldValue === null) {
            return false;
        }

        $validationResult = $this->validatorInterface->validate($fieldValue, $constraints);

        return !$validationResult->count();
    }

    /**
     * @param string|array $paramValue
     * @return string|array
     */
    private function replaceParamsMarkersWithBeanValues(object $entity, $paramValue)
    {
        if (\is_array($paramValue)) {
            foreach ($paramValue as &$param) {
                $param = $this->replaceParamsMarkersWithBeanValues($entity, $paramValue);
            }
            return $paramValue;
        }

        if (\is_bool($paramValue)) {
            return $paramValue;
        }

        return $this->checkForDateValue(
            $this->parserService->parseListValue($entity, $paramValue, null)
        );
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function checkForDateValue($value)
    {
        if (\is_string($value)) {
            $date = \DateTime::createFromFormat('d-m-Y', $value);
            if ($date instanceof \DateTime) {
                return $date;
            }

            $date = \DateTime::createFromFormat('d-m-Y H:i', $value);
            if ($date instanceof \DateTime) {
                return $date;
            }

            $date = \DateTime::createFromFormat('d-m-Y H:i:s', $value);
            if ($date instanceof \DateTime) {
                return $date;
            }
        }
        return $value;
    }

    private function logIncorrectStructure(object $action): void
    {
        $this->logger->error(
            \sprintf(
                "The enable conditions for the action `%s` doesn't have the right structure. [%s:%s]",
                $action->name,
                \get_class($action),
                $action->id
            )
        );
    }
}
