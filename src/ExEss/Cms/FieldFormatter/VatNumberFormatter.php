<?php

namespace ExEss\Cms\FieldFormatter;

/**
 * Vat number formatter
 */
class VatNumberFormatter implements FieldFormatter
{
    /**
     * Country constants
     */
    public const COUNTRY_AT = 'AT';
    public const COUNTRY_BE = 'BE';
    public const COUNTRY_CZ = 'CZ';
    public const COUNTRY_DE = 'DE';
    public const COUNTRY_CY = 'CY';
    public const COUNTRY_DK = 'DK';
    public const COUNTRY_EE = 'EE';
    public const COUNTRY_GR = 'GR';
    public const COUNTRY_ES = 'ES';
    public const COUNTRY_FI = 'FI';
    public const COUNTRY_FR = 'FR';
    public const COUNTRY_GB = 'GB';
    public const COUNTRY_HU = 'HU';
    public const COUNTRY_IE = 'IE';
    public const COUNTRY_IT = 'IT';
    public const COUNTRY_LT = 'LT';
    public const COUNTRY_LU = 'LU';
    public const COUNTRY_LV = 'LV';
    public const COUNTRY_MT = 'MT';
    public const COUNTRY_NL = 'NL';
    public const COUNTRY_PL = 'PL';
    public const COUNTRY_PT = 'PT';
    public const COUNTRY_SE = 'SE';
    public const COUNTRY_SI = 'SI';
    public const COUNTRY_SK = 'SK';

    private string $defaultCountryCode = self::COUNTRY_BE;

    /**
     * @inheritdoc
     */
    public function format(?string $value, ?array $parameters = null): ?string
    {
        if ($value) {
            //make it work for lowercase input as well (but we need it as uppercase)
            $value = \strtoupper($value);
            $this->setDefaultCountryCode($parameters);

            $pure = \preg_replace('/[^0-9a-zA-Z]/', '', $value);

            if (\strlen($pure) === 9
                && 0 !== \strpos($pure, '0')
                && \is_numeric(\substr($pure, 1, 2))
            ) {
                return $this->defaultCountryCode . '0' . $pure;
            } elseif (\strlen($pure) === 10
                && 0 === \strpos($pure, '0')
                && \is_numeric(\substr($pure, 1, 2))
            ) {
                return $this->defaultCountryCode . $pure;
            } elseif (!\is_numeric(\substr($pure, 0, 2))
                && \is_numeric(\substr($pure, 3, 2))
            ) {
                return $pure;
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
     * @see https://www.safaribooksonline.com/library/view/regular-expressions-cookbook/9781449327453/ch04s21.html
     * @see https://github.com/bcit-ci/CodeIgniter/wiki/European-Vat-Checker
     */
    public function isValid(?string $value, ?array $parameters = null): bool
    {
        //make it work for lowercase input as well (but we need it as uppercase)
        $value = \strtoupper($value);
        $country = \substr($value, 0, 2);

        if (empty($country)) {
            return false;
        }

        switch ($country) {
            case self::COUNTRY_AT:
                $regex = '/^U[0-9]{8}$/';
                break;
            case self::COUNTRY_BE:
                $regex = '/^0[0-9]{9}$/';

                // Belgian VAT numbers can be checked by MOD97
                $number = \str_replace(self::COUNTRY_BE, '', $value);

                if (\strlen($number) !== 10) {
                    return false;
                }

                $part1 = \substr($number, 1, 7);
                $part2 = \substr($number, 8, 2);
                $mod97 = $part1 % 97;

                if ($part2 + $mod97 !== 97) {
                    return false;
                }

                break;
            case self::COUNTRY_CZ:
                $regex = '/^[0-9]{8,10}$/';
                break;
            case self::COUNTRY_DE:
            case self::COUNTRY_EE:
            case self::COUNTRY_PT:
            case self::COUNTRY_GR:
                $regex = '/^[0-9]{9}$/';
                break;
            case self::COUNTRY_CY:
                $regex = '/^[0-9]{8}[A-Z]$/';
                break;
            case self::COUNTRY_DK:
            case self::COUNTRY_FI:
            case self::COUNTRY_HU:
            case self::COUNTRY_LU:
            case self::COUNTRY_MT:
            case self::COUNTRY_SI:
                $regex = '/^[0-9]{8}$/';
                break;
            case self::COUNTRY_ES:
                $regex = '/^[0-9A-Z][0-9]{7}[0-9A-Z]$/';
                break;
            case self::COUNTRY_FR:
                $regex = '/^[0-9A-Z]{2}[0-9]{9}$/';
                break;
            case self::COUNTRY_GB:
                $regex = '/^([0-9]{9}|[0-9]{12})~(GD|HA)[0-9]{3}$/';
                break;
            case self::COUNTRY_IE:
                $regex = '/^[0-9][A-Z0-9\\+\\*][0-9]{5}[A-Z]$/';
                break;
            case self::COUNTRY_IT:
            case self::COUNTRY_LV:
                $regex = '/^[0-9]{11}$/';
                break;
            case self::COUNTRY_LT:
                $regex = '/^([0-9]{9}|[0-9]{12})$/';
                break;
            case self::COUNTRY_NL:
                $regex = '/^[0-9]{9}B[0-9]{2}$/';
                break;
            case self::COUNTRY_PL:
            case self::COUNTRY_SK:
                $regex = '/^[0-9]{10}$/';
                break;
            case self::COUNTRY_SE:
                $regex = '/^[0-9]{12}$/';
                break;
            default:
                return false;
                break;
        }

        $vat = \str_replace($country, '', $value);

        return \preg_match($regex, $vat);
    }
}
