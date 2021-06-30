<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Params\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorFactory
{
    protected ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array|\Symfony\Component\Validator\Constraint[] $constraints
     */
    public function createClosure(array $constraints, bool $allowNull = false): callable
    {
        return function ($value) use ($constraints, $allowNull) {
            if ($allowNull && $value === null) {
                return true;
            }
            $violations = $this->validator->validate($value, $constraints);

            // put breakpoint on the next line to see your violations
            return !$violations->count();
        };
    }

    /**
     * @param array|\Symfony\Component\Validator\Constraint[] $constraints
     */
    public function createClosureForIterator(array $constraints, bool $allowNull = false): callable
    {
        return function ($value) use ($constraints, $allowNull) {
            if ($allowNull && $value === null) {
                return true;
            }
            if (!\is_array($value) && !$value instanceof \Iterator) {
                return false;
            }
            foreach ($value as $v) {
                if ($this->validator->validate($v, $constraints)->count()) {
                    return false;
                }
            }
            return true;
        };
    }
}
