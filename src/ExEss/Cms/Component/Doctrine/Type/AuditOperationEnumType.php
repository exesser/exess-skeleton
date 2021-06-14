<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Type;

class AuditOperationEnumType extends AbstractEnumType
{
    public const INSERT = 'INSERT';
    public const UPDATE = 'UPDATE';
    public const DELETE = 'DELETE';

    public static function getValues(): array
    {
        return [
            self::INSERT => self::INSERT,
            self::UPDATE => self::UPDATE,
            self::DELETE => self::DELETE,
        ];
    }

    public function getName(): string
    {
        return AbstractEnumType::PREFIX . 'audit_operation';
    }
}
