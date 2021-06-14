<?php

namespace ExEss\Cms\Api\V8_Custom\Service;

use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Exception\JsonDecodeException;

/**
 * This is a class use to decode data before it's passed to the dwp, activiti or ...
 */
class DataCleaner
{
    /**
     * Takes an object, or array or null or string or int, whatever actually
     * and will filter out javascript if its present.
     *
     * @param mixed $data
     * @return mixed
     */
    public static function clean($data)
    {
        if (null === $data) {
            return $data;
        }

        if (\is_object($data) || \is_array($data)) {
            foreach ($data as $key => &$value) {
                $value = self::clean($value);
            }
        }

        if (\is_string($data)) {
            $data = \preg_replace('/javascript:/i', 'java script:', $data);
            $data = \str_replace(['{{', '}}'], ['[[', ']]'], $data);
            return \preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $data);
        }

        return $data;
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public static function decodeData($data, bool $doDecode = false)
    {
        if (\is_string($data)) {
            return $doDecode ? self::formatNumbers($data) : $data;
        }

        if (\is_array($data) || \is_object($data)) {
            foreach ($data as $key => $item) {
                $tmpDoDecode = $doDecode;
                if ($key === 'options') {
                    $tmpDoDecode = true;
                }

                if ($key === 'id') {
                    $tmpDoDecode = false;
                }

                if (\is_array($data)) {
                    $data[$key] = self::decodeData($item, $tmpDoDecode);
                } elseif (\is_object($data)) {
                    $data->$key = self::decodeData($item, $tmpDoDecode);
                }
            }
        }

        return $data;
    }

    public static function formatNumbers(string $line): string
    {
        $matches = [];
        // If you change this, please also change the DWP dynamic-text component
        \preg_match_all('/[\d]+[.]{1}[\d]+[.]*/', $line, $matches);
        foreach (\current($matches) as $match) {
            if (\substr($match, -1) === '.') {
                continue;
            }
            $line = \str_replace(
                $match,
                \number_format(
                    (float)$match,
                    \strlen($match) - \strrpos($match, '.') - 1,
                    ',',
                    ''
                ),
                $line
            );
        }

        return $line;
    }

    public static function replaceParamValuesAndDecodeJson(array $json, array $arguments): array
    {
        $json = \json_encode($json);
        foreach ($arguments as $key => $argument) {
            if (!\is_array($argument) && !\is_object($argument)) {
                $json = \str_replace('%' . $key . '%', $argument, $json);
            }
        }

        return \json_decode($json, true);
    }

    /**
     * @return \stdClass|array
     * @throws JsonDecodeException When $json is not a valid json string.
     */
    public static function jsonDecode(string $json, bool $assoc = false, int $depth = 512, int $options = 1)
    {
        $decoded = \json_decode($json, $assoc, $depth, $options);
        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new JsonDecodeException(\json_last_error_msg());
        }

        return $decoded;
    }

    public static function getCleanedModel(array $model, bool $removeCompleteFile = false): array
    {
        if (isset($model[Dwp::BINARY_FILE])) {
            foreach ($model[Dwp::BINARY_FILE] as $key => $file) {
                if (!isset($file['stream'])) {
                    continue;
                }

                if ($removeCompleteFile) {
                    unset($model[Dwp::BINARY_FILE][$key]);
                } else {
                    $model[Dwp::BINARY_FILE][$key]['stream'] = 'stream-not-logged';
                }
            }
        }

        return $model;
    }

    public static function translateConstantToVar(string $constant, string $suffix): string
    {
        return \preg_replace_callback('/[._-][a-z]?/', function (array $matches): string {
            return \strtoupper(\ltrim($matches[0], '._-'));
        }, \strtolower($constant)) . $suffix;
    }
}
