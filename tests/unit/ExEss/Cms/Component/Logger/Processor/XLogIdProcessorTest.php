<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\Component\Logger\Processor;

use ExEss\Bundle\CmsBundle\Component\Logger\Processor\XLogIdProcessor;
use Helper\Testcase\UnitTestCase;

class XLogIdProcessorTest extends UnitTestCase
{
    /**
     * @dataProvider getValues
     */
    public function testInvoke(?string $xLogId, array $expectedRecord): void
    {
        // Given
        $processor = new XLogIdProcessor();
        $_SERVER['HTTP_X_LOG_ID'] = $xLogId;

        // When
        $record = $processor([]);

        // Then
        $this->tester->assertEquals($expectedRecord, $record);
    }

    public function getValues(): array
    {
        return [
            'HTTP_X_LOG_ID exists' => [
                'xLogId' => $xLogId = 'some-terrific-guid',
                'expectedRecord' => [
                    'extra' => [
                        'X-LOG-ID' => $xLogId,
                    ],
                ],
            ],
            'HTTP_X_LOG_ID is null' => [
                'xLogId' => null,
                'expectedRecord' => [],
            ],
        ];
    }
}
