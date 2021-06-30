<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Doctrine\Type;

use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

class HttpMethod extends AbstractEnumType
{
    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const PATCH = 'PATCH';
    public const DELETE = 'DELETE';
    public const OPTIONS = 'OPTIONS';

    public static function getValues(): array
    {
        return [
            self::GET => 'GET',
            self::POST => 'POST',
            self::PUT => 'PUT',
            self::DELETE => 'DELETE',
            self::PATCH => 'PATCH',
        ];
    }

    public function getName(): string
    {
        return 'enum_http_method';
    }
}
