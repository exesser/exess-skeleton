<?php declare(strict_types=1);

namespace Helper;

use ExEss\Bundle\CmsBundle\Helper\DataCleaner;

class JsonHelper extends \Codeception\Module
{
    /**
     * Loads a json file, and replaces the tokens if present.
     * If you want to use json but have some dynamic data in them..
     *
     * @return array|object
     * @throws \InvalidArgumentException When the file could not be loaded.
     * @throws \InvalidArgumentException When the json could not be parsed.
     */
    public function loadJsonWithParams(string $file, array $parameters = [], bool $assoc = true)
    {
        if (!\file_exists($file)) {
            throw new \InvalidArgumentException(\sprintf('Could not load %s', $file));
        }

        $string = \file_get_contents($file);

        if (!empty($parameters)) {
            $values = \array_map(function ($value) {
                return \str_replace('\\', '\\\\', $value);
            }, \array_values($parameters));

            $tokens = \array_map(function ($token) {
                return '{{' . \trim($token) . '}}';
            }, \array_keys($parameters));

            $tokensInt = \array_map(function ($token) {
                return '"<<' . \trim($token) . '>>"';
            }, \array_keys($parameters));

            $string = \str_replace($tokens, $values, $string);
            $string = \str_replace($tokensInt, $values, $string);
        }

        return DataCleaner::jsonDecode($string, $assoc);
    }
}
