<?php declare(strict_types=1);

namespace Helper\Testcase;

if (!\class_exists('UnitTester')) {
    throw new \Exception('The UnitTester class should exist (check the tests/_support folder)');
}

use Codeception\TestCase\Test;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class UnitTestCase extends Test
{
    use MockeryPHPUnitIntegration;

    protected \UnitTester $tester;
}
