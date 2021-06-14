<?php

namespace ExEss\Cms\Validators;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Vat extends Constraint
{
    public string $message =  'The VAT `{{ vat }}` is not valid.';

    public function validatedBy(): string
    {
        return VatValidator::class;
    }
}
