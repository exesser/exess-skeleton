<?php

namespace ExEss\Cms\Api\V8_Custom\Service\User;

use Firebase\JWT\JWT;
use stdClass;

class TokenService
{
    public const JWT_ALG = 'HS512';

    public int $timeout;

    public string $uniqueKey;

    public function __construct(
        int $timeout,
        string $uniqueKey
    ) {
        $this->timeout = $timeout;
        $this->uniqueKey = $uniqueKey;
    }

    public function generateToken(string $userId): string
    {
        $token = [
            'userId' => $userId,
            'exp' => \time() + $this->timeout,
        ];

        return JWT::encode($token, $this->uniqueKey, self::JWT_ALG);
    }

    public function decodeToken(string $jwt): ?stdClass
    {
        try {
            return JWT::decode($jwt, $this->uniqueKey, \array_keys(JWT::$supported_algs));
        } catch (\Exception $exception) {
            return null;
        }
    }
}
