<?php
namespace ExEss\Cms\Config;

class ConfigResolver
{
    /**
     * Takes an array of php file paths, which should all return al array when required,
     * merges all arrays and returns the result.
     * merge is optionally recursive.
     *
     * @return array
     * @throws \RuntimeException When config file is not readable or does not contain an array.
     */
    public static function resolveFromFiles(array $files = [], bool $recursive = false): array
    {
        $configs = [];
        foreach ($files as $file) {
            if (!\file_exists($file) || !\is_readable($file)) {
                throw new \RuntimeException(\sprintf('failed to read config file: "%s"', $file));
            }
            $config = require $file;
            if (!\is_array($config)) {
                throw new \RuntimeException(\sprintf('invalid config file: "%s"', $file));
            }
            $configs[] = $config;
        }

        if (!\count($configs)) {
            return [];
        }

        return $recursive ? \array_merge_recursive(...$configs) : \array_merge(...$configs);
    }
}
