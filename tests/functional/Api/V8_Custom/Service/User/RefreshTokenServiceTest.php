<?php declare(strict_types=1);

namespace Test\Functional\Api\V8_Custom\Service\User;

use ExEss\Cms\Api\V8_Custom\Service\User\TokenService;
use Firebase\JWT\JWT;
use ExEss\Cms\Api\V8_Custom\Service\User\RefreshTokenService;
use ExEss\Cms\Test\Testcase\FunctionalTestCase;

class RefreshTokenServiceTest extends FunctionalTestCase
{
    private string $userId;

    private RefreshTokenService $refreshTokenService;

    /**
     * @var array
     */
    public array $sugarConfig;

    public function _before(): void
    {
        $this->refreshTokenService = $this->tester->grabService(RefreshTokenService::class);
        $this->userId = $this->tester->generateUser('tester');
    }

    public function _after(): void
    {
        $this->tester->cleanDatabaseForUser($this->userId);
    }

    public function testGenerateToken(): void
    {
        // Act
        $jwt = $this->refreshTokenService->generateToken($this->userId);

        // Assert
        $this->tester->assertIsString($jwt);
    }

    public function testDecodeToken(): void
    {
        // Given
        $jwt = $this->refreshTokenService->generateToken($this->userId);

        // Act
        $token = $this->refreshTokenService->decodeToken($jwt);

        // Assert
        $this->tester->assertIsObject($token);
    }

    public function testShouldRefresh(): void
    {
        // Given
        $token = [
            'userId' => $this->userId,
            'exp' => \time() + 120, // seconds
        ];

        $jwt = JWT::encode($token, $this->refreshTokenService->uniqueKey, TokenService::JWT_ALG);

        // Act
        if ($this->refreshTokenService->tokenAboutToExpire($jwt)) {
            $newJwt = $this->refreshTokenService->refreshToken($jwt);
        } else {
            $newJwt = $jwt;
        }

        // Assert
        $this->tester->assertNotEquals($jwt, $newJwt);
    }

    public function testShouldNotRefresh(): void
    {
        // Given
        $token = [
            'userId' => $this->userId,
            'exp' => \time() + 2000, // seconds
        ];

        $jwt = JWT::encode($token, $this->refreshTokenService->uniqueKey, TokenService::JWT_ALG);

        // Act
        $tokenAboutToExpire = $this->refreshTokenService->tokenAboutToExpire($jwt);

        // Assert
        $this->tester->assertFalse($tokenAboutToExpire);
    }
}
