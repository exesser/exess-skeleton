<?php

namespace ExEss\Bundle\CmsBundle\Validators;

use libphonenumber\PhoneNumberType;

class FixedPhoneNumberValidator extends PhoneNumberValidator
{
    protected function isPhoneNumberValid(\libphonenumber\PhoneNumber $phone): bool
    {
        return parent::isPhoneNumberValid($phone)
            && \in_array($this->util->getNumberType($phone), [PhoneNumberType::FIXED_LINE, PhoneNumberType::UAN]);
    }
}
