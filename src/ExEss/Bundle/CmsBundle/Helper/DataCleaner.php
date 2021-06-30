<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Helper;

use ExEss\Bundle\CmsBundle\Dictionary\Model\Dwp;

/**
 * This is a class use to decode data before it's passed to the dwp, activiti or ...
 */
class DataCleaner
{
    public const STREAM_REPLACEMENT = 'stream-not-logged';

    /**
     * Takes an object, or array or null or string or int, whatever actually
     * and will filter out javascript if its present.
     *
     * @param mixed $data
     * @return mixed
     */
    public static function cleanInput($data)
    {
        if (null === $data) {
            return $data;
        }

        if (\is_object($data) || \is_array($data)) {
            foreach ($data as &$value) {
                $value = self::cleanInput($value);
            }
        }

        if (\is_string($data)) {
            return \preg_replace(
                ['/javascript:/i', '/<script[^>]*>(.*?)<(\\\\)*\/script>/is'],
                ['java script:', ""],
                $data
            );
        }

        return $data;
    }

    /**
     * Takes an object, or array or null or string or int, whatever actually
     * and will filter out javascript if its present.
     *
     * @param mixed $data
     * @return mixed
     */
    public static function cleanOutput($data)
    {
        if (null === $data) {
            return $data;
        }

        if (\is_object($data) || \is_array($data)) {
            foreach ($data as &$value) {
                $value = self::cleanOutput($value);
            }
        }

        if (\is_string($data)) {
            return \str_replace(['{{', '}}'], ['[[', ']]'], $data);
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

        return self::jsonDecode($json);
    }

    /**
     * @return \stdClass|array
     * @throws \JsonException When $json is not a valid json string.
     */
    public static function jsonDecode(string $json, bool $assoc = true, int $depth = 512, int $options = 1)
    {
        return \json_decode($json, $assoc, $depth, \JSON_THROW_ON_ERROR | $options);
    }

    /**
     * @param mixed $json
     */
    public static function isJson($json): bool
    {
        try {
            return \is_string($json) && \is_array(self::jsonDecode($json));
        } catch (\JsonException $e) {
            return false;
        }
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
                    $model[Dwp::BINARY_FILE][$key]['stream'] = self::STREAM_REPLACEMENT;
                }
            }
        }

        return $model;
    }
}
