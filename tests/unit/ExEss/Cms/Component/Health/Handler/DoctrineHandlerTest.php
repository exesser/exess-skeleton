<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\Component\Health\Handler;

use Doctrine\DBAL\Connection;
use ExEss\Bundle\CmsBundle\Component\Health\Handler\DoctrineHandler;
use ExEss\Bundle\CmsBundle\Component\Health\Model\HealthCheckResult;
use Helper\Testcase\UnitTestCase;

class DoctrineHandlerTest extends UnitTestCase
{
    /**
     * @var Connection|\Mockery\Mock
     */
    private $connection;

    private DoctrineHandler $handler;

    public function _before(): void
    {
        // Given
        $this->connection = \Mockery::mock(Connection::class);
        $this->handler = new DoctrineHandler($this->connection);
    }

    public function testSuccess(): void
    {
        // Given
        $this->connection->shouldReceive('executeQuery')->once()->andReturn(true);

        // When
        $result = $this->handler->getHealthCheck();

        // Then
        $this->tester->assertInstanceOf(HealthCheckResult::class, $result);
        $this->tester->assertEquals(true, $result->getResult());
        $this->tester->assertEquals(HealthCheckResult::OK, $result->getMessage());
    }

    public function testFailure(): void
    {
        // Given
        $this->connection->shouldReceive('executeQuery')->once()->andThrow(\Exception::class);

        // When
        $result = $this->handler->getHealthCheck();

        // Then
        $this->tester->assertInstanceOf(HealthCheckResult::class, $result);
        $this->tester->assertEquals(false, $result->getResult());
        $this->tester->assertEquals('Database server not available', $result->getMessage());
    }
}
