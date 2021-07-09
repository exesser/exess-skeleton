<?php declare(strict_types=1);

namespace Test\Functional;

use Helper\Testcase\FunctionalTestCase;

class ContainerTest extends FunctionalTestCase
{
    /**
     * Simple functional test that asserts all services in the container can be initialized
     */
    public function testGrabAllServices(): void
    {
        foreach ($this->tester->getContainerKeys() as $serviceName) {
            try {
                $this->tester->grabService($serviceName);
                $this->tester->assertTrue(true);
            } catch (\Throwable $e) {
                $this->tester->assertTrue(false, "Grabbing service $serviceName failed: " . $e->getMessage());
            }
        }
    }
}
