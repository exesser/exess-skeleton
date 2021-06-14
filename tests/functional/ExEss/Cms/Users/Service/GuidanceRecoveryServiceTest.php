<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Users\Service;

use ExEss\Cms\Entity\User;
use ExEss\Cms\Entity\UserGuidanceRecovery;
use ExEss\Cms\Test\Testcase\FunctionalTestCase;
use ExEss\Cms\Users\Service\GuidanceRecoveryService;

class GuidanceRecoveryServiceTest extends FunctionalTestCase
{
    private string $userId;

    private GuidanceRecoveryService $handler;

    public function _before(): void
    {
        // create and log in user
        $this->tester->emptyTable('user_guidance_recovery');

        $this->userId = $this->tester->generateUser('test');
        $this->handler = $this->tester->grabService(GuidanceRecoveryService::class);
    }

    public function testGetEmptyData(): void
    {
        $this->tester->loginAsUser(
            $this->tester->grabEntityFromRepository(User::class, ['id' => $this->userId])
        );

        $this->tester->assertEquals(null, $this->handler->getNavigateArguments());
    }

    public function testGetData(): void
    {
        $data = 'recovery-data';
        $this->tester->generateUserGuidanceRecovery($this->userId, '{"data": "' . $data . '"}');

        $this->tester->loginAsUser(
            $this->tester->grabEntityFromRepository(User::class, ['id' => $this->userId])
        );

        $this->tester->assertEquals($data, $this->handler->getNavigateArguments()->data);
    }

    public function testReset(): void
    {
        $this->tester->generateUserGuidanceRecovery($this->userId, '{"not": "null"}');

        $this->tester->loginAsUser(
            $this->tester->grabEntityFromRepository(User::class, ['id' => $this->userId])
        );

        $this->handler->resetGuidanceRecoveryData();

        $this->tester->seeInRepository(
            UserGuidanceRecovery::class,
            [
                'id' => $this->userId,
                'recoveryData' => null,
            ]
        );
    }
}
