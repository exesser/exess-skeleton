<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\FLW_Flows\ResponseHandler;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Test\Testcase\UnitTestCase;
use ExEss\Cms\FLW_Flows\ResponseHandler\Handler;
use ExEss\Cms\FLW_Flows\ResponseHandler\HandlerStack;

class HandlerStackTest extends UnitTestCase
{
    public function testAllMethods(): void
    {
        $handler1 = \Mockery::mock(Handler::class);
        $stack = new HandlerStack([$handler1]);
        $this->tester->assertCount(1, $stack->toArray());

        $handler2 = new class implements Handler {
            public function shouldModify(FlowEvent $event): bool
            {
                return false;
            }

            public function __invoke(FlowEvent $event): void
            {
            }
        };
        $stack->push($handler2);
        $this->tester->assertCount(2, $stack->toArray());

        $this->tester->assertTrue($stack->has(\get_class($handler1)));
        $this->tester->assertSame($stack->get(\get_class($handler2)), $handler2);
    }
}
