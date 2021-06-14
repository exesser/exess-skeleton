<?php

namespace ExEss\Cms\Validators;

use ExEss\Cms\FieldFormatter\VatNumberFormatter;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VatValidator extends ConstraintValidator
{
    private VatNumberFormatter $vatNumberFormatter;

    public function __construct(VatNumberFormatter $vatNumberFormatter)
    {
        $this->vatNumberFormatter = $vatNumberFormatter;
    }

    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (false === $this->vatNumberFormatter->isValid($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ vat }}', $value)
                ->addViolation();
        }
    }
}
