<?php

namespace ExEss\Cms\Validators;

/**
 * @Annotation
 */
class FixedPhoneNumber extends PhoneNumber
{
    public function validatedBy(): string
    {
        return FixedPhoneNumberValidator::class;
    }
}
