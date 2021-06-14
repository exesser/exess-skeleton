<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractEnumType extends Type
{
    public const PREFIX = 'enum_';

    abstract public static function getValues(): array;

    abstract public function getName(): string;

    public function getEnums(): string
    {
        $options = \array_keys(static::getValues());
        \sort($options);
        return '\'' . \implode('\', \'', $options) . '\'';
    }

    public function accepts(string $value): bool
    {
        return \array_key_exists($value, static::getValues());
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        \preg_match('~<ENUMS>(.*)<\/ENUMS>~', $fieldDeclaration['comment'], $match);

        $enums = $this->getEnums();
        if (isset($match[1])) {
            $enums = $match[1];
        }

        return "ENUM($enums)";
    }

    /**
     * @param mixed $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if (\is_null($value)) {
            return null;
        }

        return (string) $value;
    }

    /**
     * @param mixed $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (\is_null($value)) {
            return null;
        }

        $values = static::getValues();

        if (isset($values[$value])) {
            return (string) $value;
        }

        throw new \InvalidArgumentException("$value is an invalid option for '" . $this->getName());
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
