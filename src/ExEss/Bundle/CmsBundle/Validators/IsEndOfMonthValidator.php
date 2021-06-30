<?php

namespace ExEss\Bundle\CmsBundle\Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsEndOfMonthValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (empty($value) || !$value instanceof \DateTimeInterface) {
            return;
        }

        $endOfMonth = clone $value;
        $endOfMonth->modify('last day of this month');

        if ($value->format('d') !== $endOfMonth->format('d')) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value->format('d-m-Y'))
                ->addViolation();
        }
    }
}
