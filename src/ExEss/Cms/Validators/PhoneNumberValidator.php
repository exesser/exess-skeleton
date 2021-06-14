<?php

namespace ExEss\Cms\Validators;

use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneNumberValidator extends ConstraintValidator
{
    protected PhoneNumberUtil $util;

    public function __construct(string $defaultCountryCode, PhoneNumberUtil $util)
    {
        $this->util = $util;
        $this->defaultCountryCode = $defaultCountryCode;
    }

    private string $defaultCountryCode;

    /**
     * @param mixed $value
     * @param Constraint|PhoneNumber $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (empty(\trim($value))) {
            return;
        }

        try {
            $phone = $this->util->parse($value, $this->defaultCountryCode);

            if (false === $this->isPhoneNumberValid($phone)) {
                $this->invalidate($value, $constraint);
            }
        } catch (\Exception $exception) {
            $this->invalidate($value, $constraint);
        }
    }

    /**
     * Checks if a phone number is valid
     */
    protected function isPhoneNumberValid(\libphonenumber\PhoneNumber $phone): bool
    {
        try {
            return $this->util->isValidNumber($phone);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function invalidate(string $value, PhoneNumber $constraint): void
    {
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
