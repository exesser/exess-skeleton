<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms;

use ExEss\Cms\Test\Testcase\FunctionalTestCase;

class ContainerTest extends FunctionalTestCase
{
    public function testConstructAllServices(): void
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
