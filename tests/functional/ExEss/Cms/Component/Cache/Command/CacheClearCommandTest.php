<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Component\Cache\Command;

use ExEss\Bundle\CmsBundle\Component\Cache\CacheAdapterFactory;
use ExEss\Bundle\CmsBundle\Component\Cache\Command\CacheClearCommand;
use ExEss\Bundle\CmsBundle\Component\Cache\Dictionary;
use Mockery;
use Helper\Testcase\FunctionalTestCase;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Console\Tester\CommandTester;

class CacheClearCommandTest extends FunctionalTestCase
{
    private const MESSAGE_TYPE = 'Flushing caches';
    private const MESSAGE_SUCCESS = 'Flushed all \'%s\' entries';
    private const MESSAGE_ERROR = 'Failed flushing entries for \'%s\'';

    /**
     * @dataProvider cacheSuccessProvider
     */
    public function testClearRedisCache(bool $success, string $message): void
    {
        // Given
        $cacheAdapterFactory = Mockery::mock(CacheAdapterFactory::class);
        $this->tester->mockService(CacheAdapterFactory::class, $cacheAdapterFactory);
        $totalPools = \count(Dictionary::CACHE_POOLS);

        $cacheAdapter = Mockery::mock(AdapterInterface::class);
        $cacheAdapter->shouldReceive('clear')->times($totalPools)->andReturn($success);

        $cacheAdapterFactory
            ->shouldReceive('create')
            ->times($totalPools)
            ->andReturn($cacheAdapter);

        $command = $this->tester->grabService(CacheClearCommand::class);
        $commandTester = new CommandTester($command);

        // When
        $commandTester->execute([]);

        // Then
        $output = $commandTester->getDisplay();

        $this->tester->assertStringContainsString(self::MESSAGE_TYPE, $output);

        if (!$success) {
            $this->tester->assertEquals(1, $commandTester->getStatusCode(), 'Expected error code');
        }

        foreach (\array_keys(Dictionary::CACHE_POOLS) as $pool) {
            $this->tester->assertStringContainsString(
                \sprintf($message, $pool),
                $output
            );
        }
    }

    public function cacheSuccessProvider(): array
    {
        return [
            'Cache Clear should succeed' => [
                true,
                self::MESSAGE_SUCCESS,
            ],
            'Cache Clear should fail' => [
                false,
                self::MESSAGE_ERROR,
            ],
        ];
    }
}
