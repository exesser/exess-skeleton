<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\Helper;

use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
use Helper\Testcase\UnitTestCase;
use stdClass;

class DataCleanerTest extends UnitTestCase
{
    public function testDataDecoderWithArray(): void
    {
        $object = new stdClass();
        $object->test = "test&#39;";
        $object->test2 = "empty";
        $object->test3 = "test#";

        $input = [
            "test" => "test&#39;",
            "test2" => "empty",
            "test3" => "test&#35;",
            "test4" => [
                "test" => "test'",
                "test2" => "empty",
                "test3" => "test#",
            ],
            "test5" => $object
        ];

        $output = DataCleaner::decodeData($input);

        $expectedObject = new stdClass();
        $expectedObject->test = "test&#39;";
        $expectedObject->test2 = "empty";
        $expectedObject->test3 = "test#";

        $this->tester->assertEquals(
            [
                "test" => "test&#39;",
                "test2" => "empty",
                "test3" => "test&#35;",
                "test4" => [
                    "test" => "test'",
                    "test2" => "empty",
                    "test3" => "test#",
                ],
                "test5" => $expectedObject
            ],
            $output
        );
    }

    public function testDataDecoderWithObject(): void
    {
        $input = new stdClass();
        $input->test = "test&#39;";
        $input->test2 = "empty";
        $input->test3 = "test&#35;";
        $input->test4 = [
            "test" => "test'",
            "test2" => "empty",
            "test3" => "test#",
        ];

        $output = DataCleaner::decodeData($input);

        $expected = new stdClass();
        $expected->test = "test&#39;";
        $expected->test2 = "empty";
        $expected->test3 = "test&#35;";
        $expected->test4 = [
            "test" => "test'",
            "test2" => "empty",
            "test3" => "test#"
        ];

        $this->tester->assertEquals(
            $expected,
            $output
        );
    }
    public function testJsonDecodeSuccess(): void
    {
        $decoded = DataCleaner::jsonDecode(
            '{
              "param1": "1",
              "param2": 2,
              "param3": {
                "param4": "4",
                "param5": 5
              }
            }',
            true
        );

        $this->tester->assertArrayEqual(
            ['param1' => '1', 'param2' => 2, 'param3' => ['param4' => '4', 'param5' => 5]],
            $decoded
        );
    }

    public function testJsonDecodeError(): void
    {
        $this->tester->expectThrowable(\JsonException::class, function (): void {
            DataCleaner::jsonDecode('azerty', true);
        });

        $this->tester->expectThrowable(\JsonException::class, function (): void {
            DataCleaner::jsonDecode('', true);
        });
    }

    public function testDecodeData(): void
    {
        // Given
        $input = [
            "test1" => "123.456",
            "test2" => "test'",
            "test3" => "",
            "options" => [
                "test11" => "123.456",
                "id" => "123.456",
                "test12" => "123.456",
                "test2" => "test&#39;",
                "test3" => "",
            ],
            "test5" => (object) [
                "test1" => "123.456",
                "test2" => "test'",
                "test3" => "",
            ]
        ];

        $expectedOutput = [
            "test1" => "123.456",
            "test2" => "test'",
            "test3" => "",
            "options" => [
                "test11" => "123,456",
                "id" => "123.456",
                "test12" => "123,456",
                "test2" => "test&#39;",
                "test3" => "",
            ],
            "test5" => (object) [
                "test1" => "123.456",
                "test2" => "test'",
                "test3" => "",
            ]
        ];

        // When
        $realOutput = DataCleaner::decodeData($input);

        // Then
        $this->tester->assertEquals($expectedOutput, $realOutput);
    }
}
