<?php

namespace ExEss\Bundle\CmsBundle\Validators;

use libphonenumber\PhoneNumberType;

class MobilePhoneNumberValidator extends PhoneNumberValidator
{
    protected function isPhoneNumberValid(\libphonenumber\PhoneNumber $phone): bool
    {
        return parent::isPhoneNumberValid($phone)
               && $this->util->getNumberType($phone) === PhoneNumberType::MOBILE;
    }
}
