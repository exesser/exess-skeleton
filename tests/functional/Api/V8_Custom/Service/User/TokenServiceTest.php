<?php declare(strict_types=1);

namespace Test\Functional\Api\V8_Custom\Service\User;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\User\TokenService;
use Helper\Testcase\FunctionalTestCase;

class TokenServiceTest extends FunctionalTestCase
{
    private string $userId;
    private TokenService $tokenService;

    public function _before(): void
    {
        $this->tokenService = $this->tester->grabService(TokenService::class);

        // Given
        $this->userId = $this->tester->generateUser('tester');
    }

    public function _after(): void
    {
        $this->tester->cleanDatabaseForUser($this->userId);
    }

    public function testGenerateToken(): void
    {
        // When
        $jwt = $this->tokenService->generateToken($this->userId);

        // Then
        $this->tester->assertIsString($jwt);
    }

    public function testDecodeToken(): void
    {
        // Given
        $jwt = $this->tokenService->generateToken($this->userId);

        // When
        $token = $this->tokenService->decodeToken($jwt);

        // Then
        $this->tester->assertIsObject($token);
    }
}
