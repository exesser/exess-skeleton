<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Compiler;

use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\PathResolverOptions;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Query\Conditions;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\ExternalObjectPiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\CompiledPath;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\MethodPiece;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;

class ExternalObjectCompiler
{
    public const REGEX_EXTERNAL_HANDLER = '/([^%.]*)Handler([^%.]*)/';

    public const JSON_ENCLOSURE = '##';
    public const ARRAY_BRACES = '[]';

    public static function shouldHandle(string $entityKey): bool
    {
        return \preg_match(self::REGEX_EXTERNAL_HANDLER, $entityKey) === 1;
    }

    /**
     * This method will check if the entire string starts and ends with # which is the indicator for literal json format
     */
    public static function hasJsonFormat(string $json): bool
    {
        return \substr($json, 0, \strlen(self::JSON_ENCLOSURE)) === self::JSON_ENCLOSURE
            && (
                \substr(
                    $json,
                    -\strlen(self::JSON_ENCLOSURE)
                ) === self::JSON_ENCLOSURE ||
                \substr(
                    $json,
                    -\strlen(self::JSON_ENCLOSURE . self::ARRAY_BRACES)
                ) === self::JSON_ENCLOSURE . self::ARRAY_BRACES
            );
    }

    /**
     * @param mixed $baseEntity
     */
    public function __invoke(
        CompiledPath $path,
        string $entityKey,
        $baseEntity,
        Conditions $conditions,
        PathResolverOptions $options
    ): void {
        if (self::hasJsonFormat($conditions->getRelation())) {
            $conditions->setRelation(\str_replace(self::JSON_ENCLOSURE, '', $conditions->getRelation()));
            $explodedRelation = \explode('|', $conditions->getRelation());
            $arguments = \implode(' AND ', $conditions->getWhere());

            $piece = new ExternalObjectPiece([
                \current($explodedRelation),
                DataCleaner::jsonDecode($arguments),
                $conditions->getLimit() ?? -1
            ]);
        } else {
            $explodedRelation = \explode('|', $conditions->getRelation());
            $piece = new ExternalObjectPiece([
                \current($explodedRelation),
                self::getArgumentsFrom(\implode(' AND ', $conditions->getWhere())),
                $conditions->getLimit() ?? -1
            ]);
        }

        $path[$conditions->getRelation()] = $piece;

        $explodedPath = \explode('|', \substr($entityKey, \strpos($entityKey, ')') + 1));
        \array_shift($explodedPath);
        $thisBeanKey = $conditions->getRelation();
        foreach ($explodedPath as $part) {
            $thisBeanKey .= '|'.$part;
            $path[$thisBeanKey] = new MethodPiece('get' . \ucfirst($part));
        }
    }

    /**
     * Fat entity key has a syntax such as this:
     *      recordId:id,onlyNotPaid:true
     *
     * @throws \InvalidArgumentException In case no handler could be found in the key.
     */
    public static function getArgumentsFrom(string $argumentString): array
    {
        \preg_match_all('/(?<keys>[^:,]*):(?<values>[^,]*)/', \str_replace(' ', '', $argumentString), $matches);

        if (!isset($matches['keys'], $matches['values'])) {
            throw new \InvalidArgumentException('Invalid argument string supplied');
        }
        $arguments = [];
        foreach ($matches['keys'] as $index => $match) {
            $value = $matches['values'][$index];
            if ($value === 'true') {
                $value = true;
            } elseif ($value === 'false') {
                $value = false;
            }
            $arguments[$match] = $value;
        }

        return $arguments;
    }
}
