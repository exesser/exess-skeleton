<?php

namespace ExEss\Cms\Validators;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PhoneNumber extends Constraint
{
    public string $message =  'The number `{{ value }}` is not a valid phone number';

    public function validatedBy(): string
    {
        return PhoneNumberValidator::class;
    }
}
