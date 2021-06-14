<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\Validators\Factory;

use ExEss\Cms\Test\Testcase\UnitTestCase;
use ExEss\Cms\Validators\Exception\ConstraintNotFoundException;
use ExEss\Cms\Validators\Factory\ConstraintFactory;
use Symfony\Component\Validator\Constraints\EqualTo;

class ConstraintFactoryTest extends UnitTestCase
{
    public function testCanConstruct(): void
    {
        $factory = new ConstraintFactory([]);
        $this->tester->assertInstanceOf(ConstraintFactory::class, $factory);
    }

    public function testCanCreateConstraintFromName(): void
    {
        $factory = new ConstraintFactory(["EqualTo" => EqualTo::class]);
        $this->tester->assertInstanceOf(
            EqualTo::class,
            $factory->createFromName('EqualTo', $params = ['value' => 'a_value'])
        );
    }

    public function testShouldThrowExceptionWhenConstraintIsNotFound(): void
    {
        // Then
        $this->tester->expectThrowable(
            ConstraintNotFoundException::class,
            function (): void {
                // When
                $factory = new ConstraintFactory([]);
                $factory->createFromName('SomeBullshitConstraint');
            }
        );
    }
}
