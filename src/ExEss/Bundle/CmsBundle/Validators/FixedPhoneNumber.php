<?php

namespace ExEss\Bundle\CmsBundle\Validators;

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
