<?php declare(strict_types=1);

namespace Helper;

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
            $tokens = \array_map(function ($token) {
                return '{{' . \trim($token) . '}}';
            }, \array_keys($parameters));

            $tokensInt = \array_map(function ($token) {
                return '"<<' . \trim($token) . '>>"';
            }, \array_keys($parameters));

            $string = \str_replace($tokens, \array_values($parameters), $string);
            $string = \str_replace($tokensInt, \array_values($parameters), $string);
        }

        $parsed = \json_decode($string, $assoc);

        if (false === $parsed) {
            throw new \InvalidArgumentException('Json could not be parsed');
        }

        return $parsed;
    }
}
