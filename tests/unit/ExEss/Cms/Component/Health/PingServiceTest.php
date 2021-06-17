<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\Component\Health;

use ExEss\Cms\Component\Health\PingService;
use Helper\Testcase\UnitTestCase;

class PingServiceTest extends UnitTestCase
{
    public function testResult(): void
    {
        // Given
        $service = new PingService();

        // When
        $result = $service->getResult();

        // Then
        $this->tester->assertEquals(
            '<?xml version="1.0"?><rs-response><result>true</result></rs-response>',
            $result
        );
    }
}
