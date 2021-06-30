<?php

namespace ExEss\Bundle\CmsBundle\Validators;

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
