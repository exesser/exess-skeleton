<?php
namespace ExEss\Cms\Test\Testcase;

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
