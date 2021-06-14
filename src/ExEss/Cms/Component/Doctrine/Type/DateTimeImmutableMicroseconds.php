<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeImmutableType;

class DateTimeImmutableMicroseconds extends DateTimeImmutableType
{
    public function getName(): string
    {
        return 'datetime_immutable_microseconds';
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getDateTimeTypeDeclarationSQL($fieldDeclaration) . '(6)';
    }

    /**
     * @inheritDoc
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof \DateTimeImmutable) {
            $dateTimeFormat = $platform->getDateTimeFormatString();
            return $value->format("{$dateTimeFormat}.u");
        }

        return parent::convertToDatabaseValue($value, $platform);
    }
}
