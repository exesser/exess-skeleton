<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Params\Normalizer;

class Trim
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function normalize($value)
    {
        if (\is_string($value) && $value === '') {
            return null;
        }

        return \is_string($value) || \is_int($value) ? \trim((string) $value): $value;
    }

    public static function asClosure(): callable
    {
        return function ($resolver, $value) {
            return (new self)->normalize($value);
        };
    }
}
