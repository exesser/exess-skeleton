<?php declare(strict_types=1);

namespace ExEss\Cms\Factory;

final class JsonMapperFactory
{
    public static function create(): \JsonMapper
    {
        $mapper = new \JsonMapper();
        $mapper->bIgnoreVisibility = true;

        return $mapper;
    }
}
