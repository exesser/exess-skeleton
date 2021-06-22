<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class AbstractLargeEnumType extends AbstractEnumType
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }
}
