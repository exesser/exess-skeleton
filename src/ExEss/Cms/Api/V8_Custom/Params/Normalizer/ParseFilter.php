<?php

namespace ExEss\Cms\Api\V8_Custom\Params\Normalizer;

use ExEss\Cms\Api\V8_Custom\Service\DataCleaner;

class ParseFilter
{
    public static function normalize(string $value): array
    {
        $value = DataCleaner::decodeData($value);
        $filter = [];
        if (!empty($value)) {
            $a = \explode(',', $value);
            foreach ($a as $b) {
                $c = \explode('=', $b);
                $filter[$c[0]] = $c[1];
            }
        }

        return $filter;
    }
}
