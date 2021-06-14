<?php
namespace ExEss\Cms\Parser\Query;

use ExEss\Cms\Parser\Compiler\ExternalObjectCompiler;

/**
 * Parses a given piece into where / limit / order / relation parts
 *
 * No to be evaluated expressions should be translated into values here,
 * this should be done upon getting the values of conditions.
 */
class ConditionsParser
{
    public const NEW_ENCLOSURE_START = '{';
    public const NEW_ENCLOSURE_END = '}';
    public const OLD_ENCLOSURE_START = '(';
    public const OLD_ENCLOSURE_END = ')';

    public static function parse(string $piece): Conditions
    {
        $conditions = new Conditions($piece);

        if (!ExternalObjectCompiler::hasJsonFormat($piece)
            && \preg_match('/'.self::NEW_ENCLOSURE_START.'(.*?)'.self::NEW_ENCLOSURE_END.'/', $piece, $matches)
        ) {
            $queryOptions = \explode(';', $matches[1]);
            foreach ($queryOptions as $queryOption) {
                list($key, $value) = \explode(':', \trim($queryOption));
                switch (\strtolower($key)) {
                    case 'order':
                        $conditions->setOrder($value);
                        break;
                    case 'where':
                        $conditions->setWhere([$value]);
                        break;
                }
            }
            $piece = \str_replace($matches[0], '', $piece);
        }

        // check for limit or not
        if (\substr($piece, -2) === ExternalObjectCompiler::ARRAY_BRACES) {
            $conditions->setLimit(null);
            $piece = \str_replace(ExternalObjectCompiler::ARRAY_BRACES, '', $piece);
        }

        // this overwrites any previously set where clause!
        if (\preg_match('/\\'.self::OLD_ENCLOSURE_START.'(.*?)\\'.self::OLD_ENCLOSURE_END.'/', $piece, $matches)
        ) {
            $conditions->setWhere(\explode(';', $matches[1]));
            $piece = \str_replace($matches[0], '', $piece);
        }

        $conditions->setRelation($piece);

        return $conditions;
    }
}
