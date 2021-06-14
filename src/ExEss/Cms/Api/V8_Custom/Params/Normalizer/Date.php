<?php

namespace ExEss\Cms\Api\V8_Custom\Params\Normalizer;

use DateTimeImmutable;

class Date
{
    public function normalize(string $value): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    }

    public static function asClosure(): callable
    {
        return function ($resolver, $value) {
            return (new self)->normalize($value);
        };
    }
}
