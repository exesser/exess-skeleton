<?php

namespace ExEss\Cms\Validators;

/**
 * @Annotation
 */
class MobilePhoneNumber extends PhoneNumber
{
    public function validatedBy(): string
    {
        return MobilePhoneNumberValidator::class;
    }
}
