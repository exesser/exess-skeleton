<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\User;

use Firebase\JWT\JWT;
use stdClass;

class TokenService
{
    public const JWT_ALG = 'HS512';
    public int $timeout;
    public string $secret;

    public function __construct(
        int $timeout,
        string $secret
    ) {
        $this->timeout = $timeout;
        $this->secret = $secret;
    }

    public function generateToken(string $userId): string
    {
        $token = [
            'userId' => $userId,
            'exp' => \time() + $this->timeout,
        ];

        return JWT::encode($token, $this->secret, self::JWT_ALG);
    }

    public function decodeToken(string $jwt): ?stdClass
    {
        try {
            return JWT::decode($jwt, $this->secret, \array_keys(JWT::$supported_algs));
        } catch (\Exception $exception) {
            return null;
        }
    }
}
