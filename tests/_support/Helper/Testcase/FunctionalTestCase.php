<?php declare(strict_types=1);

namespace Helper\Testcase;

if (!\class_exists('FunctionalTester')) {
    throw new \Exception('The FunctionalTester class should exist (check the tests/_support folder)');
}

use Codeception\TestCase\Test;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * @method markTestSkipped(string $message)
 */
class FunctionalTestCase extends Test
{
    use MockeryPHPUnitIntegration;

    protected \FunctionalTester $tester;
}
