<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class AbstractLargeEnumType extends AbstractEnumType
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }
}
