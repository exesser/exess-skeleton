<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\Component\Logger\Processor;

use Mockery\MockInterface;
use ExEss\Bundle\CmsBundle\Component\Logger\Processor\NeededHeadersProcessor;
use ExEss\Bundle\CmsBundle\Component\Session\Headers;
use ExEss\Bundle\CmsBundle\Component\Session\User\UserInterface;
use Helper\Testcase\UnitTestCase;

class NeededHeadersProcessorTest extends UnitTestCase
{
    /**
     * @dataProvider getValues
     */
    public function testInvoke(
        array $expectedRecord,
        ?UserInterface $user,
        Headers $neededHeaders
    ): void {
        // Given
        if ($neededHeaders instanceof MockInterface) {
            $neededHeaders->shouldReceive('setUser')
                ->with($user);
            $neededHeaders->shouldReceive('getIterator')
                ->andReturn(new \ArrayObject([]));
        }

        $processor = new NeededHeadersProcessor($neededHeaders, $user);

        // When
        $record = $processor([]);

        // Then
        $this->tester->assertEquals($expectedRecord, $record);
    }

    public function getValues(): array
    {
        return [
            'user is mocked' => [
                'expectedRecord' => [
                    // empty as everything is mocked
                ],
                'user' => \Mockery::mock(UserInterface::class),
                'neededHeaders' => \Mockery::mock(Headers::class),
            ],
            'user is null' => [
                'expectedRecord' => [
                    'extra' => $fakeHeaders = [
                        'key1' => 'header1',
                        'key2' => 'header2',
                    ],
                ],
                'user' => null,
                'neededHeaders' => new Headers($fakeHeaders),
            ],
        ];
    }
}
