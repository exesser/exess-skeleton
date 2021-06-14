<?php

namespace ExEss\Cms\Api\V8_Custom\Service\User;

class RefreshTokenService extends TokenService
{
    public int $refreshTime;

    public function __construct(int $timeout, int $refreshTime, string $uniqueKey)
    {
        parent::__construct($timeout, $uniqueKey);
        $this->refreshTime = $refreshTime;
    }

    public function tokenAboutToExpire(string $jwt): bool
    {
        if ($token = $this->decodeToken($jwt)) {
            $expirationTime = (int) $token->exp;

            if ($expirationTime - \time() < $this->refreshTime) {
                return true;
            }
        }

        return false;
    }

    public function refreshToken(string $jwt): string
    {
        $currentToken = $this->decodeToken($jwt);
        return $this->generateToken($currentToken->userId);
    }
}
