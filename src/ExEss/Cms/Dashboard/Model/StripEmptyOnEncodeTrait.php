<?php
namespace ExEss\Cms\Dashboard\Model;

trait StripEmptyOnEncodeTrait
{
    public function jsonSerialize(): array
    {
        $properties = [];
        foreach (\get_object_vars($this) as $property => $value) {
            if ((\is_array($value) && !empty($value))
                || (!\is_array($value) && $value !== null)
            ) {
                $properties[$property] = $value;
            }
        }

        return $properties;
    }
}
