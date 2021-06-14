<?php

namespace ExEss\Cms\FieldFormatter;

/**
 * Format fields and validate whether their format is correct
 */
interface FieldFormatter
{
    /**
     * Format value
     */
    public function format(?string $value, ?array $parameters = null): ?string;

    /**
     * Validate if formatting of value is correct
     */
    public function isValid(?string $value, ?array $parameters = null): bool;
}
