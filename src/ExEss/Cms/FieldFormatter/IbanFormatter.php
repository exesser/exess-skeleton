<?php

namespace ExEss\Cms\FieldFormatter;

class IbanFormatter implements FieldFormatter
{
    public function format(?string $value, ?array $parameters = null): ?string
    {
        if ($value === null) {
            return $value;
        }

        return \strtoupper(\preg_replace("/[^A-Za-z0-9]/", '', $value));
    }

    public function isValid(?string $value, ?array $parameters = null): bool
    {
        $result = $this->format($value);
        return $result === $value;
    }
}
