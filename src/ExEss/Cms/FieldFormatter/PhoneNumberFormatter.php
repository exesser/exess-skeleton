<?php

namespace ExEss\Cms\FieldFormatter;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * Phone number formatter
 */
class PhoneNumberFormatter implements FieldFormatter
{
    private string $defaultCountryCode;

    private PhoneNumberUtil $phoneNumberUtil;

    public function __construct(string $defaultCountryCode, PhoneNumberUtil $phoneNumberUtil)
    {
        $this->defaultCountryCode = $defaultCountryCode;
        $this->phoneNumberUtil = $phoneNumberUtil;
    }

    /**
     * @inheritdoc
     */
    public function format(?string $value, ?array $parameters = null): ?string
    {
        if ($value) {
            $this->setDefaultCountryCode($parameters);

            try { //The underlying lib throws an exception if the string 'does not look like a phone number'
                $result = $this->formatPhoneNumber($value);

                return $result ?? $value;
            } catch (\Exception $e) {
                // do nothing this library is only used for formatting in case it detects a valid number
            }
        }

        return $value;
    }

    private function setDefaultCountryCode(?array $parameters = null): void
    {
        if (!empty($parameters['countryCode'])) {
            $this->defaultCountryCode = $parameters['countryCode'];
        }
    }

    /**
     * @inheritdoc
     */
    public function isValid(?string $value, ?array $parameters = null): bool
    {
        $this->setDefaultCountryCode($parameters);

        try { //The underlying lib throws an exception if the string 'does not look like a phone number'
            $result = $this->formatPhoneNumber($value);

            return $result === $value;
        } catch (\Exception $e) {
            // do nothing this library is only used for formatting in case it detects a valid number
        }

        return false;
    }

    protected function formatPhoneNumber(?string $value): ?string
    {
        $phone = $this->phoneNumberUtil->parse($value, $this->defaultCountryCode);

        if ($this->phoneNumberUtil->isValidNumber($phone)) {
            return $this->phoneNumberUtil->format($phone, PhoneNumberFormat::INTERNATIONAL);
        }

        return null;
    }

    public function formatForEmc(?string $value): ?string
    {
        if ($this->isValid($value)) {
            throw new \DomainException('Phone number ' . $value . ' is invalid');
        }

        return \str_replace('+', '00', $value);
    }
}
