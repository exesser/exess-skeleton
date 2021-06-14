<?php declare(strict_types=1);

namespace ExEss\Cms\Factory;

use Slim\Http\Environment;

final class EnvironmentFactory
{
    public function create(): Environment
    {
        return self::createFromServer($_SERVER);
    }

    public static function createFromServer(array $serverParams): Environment
    {
        $serverParams['HTTPS'] = self::isSSLFromParams($serverParams) ? 'on' : 'off';

        return new Environment($serverParams);
    }

    public static function isSSLFromParams(array $serverParams): bool
    {
        return (!empty($serverParams['HTTPS']) && $serverParams['HTTPS'] !== 'off')
            || (!empty($serverParams['HTTP_X_FORWARDED_PROTO']) && $serverParams['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (!empty($serverParams['HTTP_X_FORWARDED_SSL']) && $serverParams['HTTP_X_FORWARDED_SSL'] === 'on')
        ;
    }

    public function isSSL(): bool
    {
        return self::isSSLFromParams($_SERVER);
    }
}
