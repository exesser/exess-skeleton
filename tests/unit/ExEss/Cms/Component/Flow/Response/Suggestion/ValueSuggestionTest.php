<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\Component\Flow\Response\Suggestion;

use ExEss\Bundle\CmsBundle\Component\Flow\Response\Suggestion\SuggestionInterface;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Suggestion\ValueSuggestion;
use Helper\Testcase\UnitTestCase;
use JsonSerializable;

class ValueSuggestionTest extends UnitTestCase
{
    public function testCanInstantiate(): void
    {
        $suggestion = new ValueSuggestion(1, 'some name');
        $this->tester->assertInstanceOf(ValueSuggestion::class, $suggestion);
        $this->tester->assertInstanceOf(SuggestionInterface::class, $suggestion);
        $this->tester->assertInstanceOf(JsonSerializable::class, $suggestion);
    }

    public function testCanBecomeArray(): void
    {
        $suggestion = new ValueSuggestion('some_value', 'some_name', true);
        $asArray = (array)$suggestion->jsonSerialize();
        $this->tester->assertArrayHasKey('name', $asArray);
        $this->tester->assertArrayHasKey('value', $asArray);
        $this->tester->assertArrayHasKey('disabled', $asArray);
        $this->tester->assertEquals('some_value', $asArray['value']);
        $this->tester->assertEquals('some_name', $asArray['name']);
        $this->tester->assertTrue($asArray['disabled']);
    }

    public function testCanGetValue(): void
    {
        $suggestion = new ValueSuggestion('some_value', 'some_name');
        $this->tester->assertEquals('some_value', $suggestion->getValue());

        $suggestion = new ValueSuggestion(12345, 'some_name');
        $this->tester->assertEquals('12345', $suggestion->getValue());
    }

    public function testCanGetName(): void
    {
        $suggestion = new ValueSuggestion('some_value', 'some_name');
        $this->tester->assertEquals('some_name', $suggestion->getName());
    }

    public function testCanGetDisabledCondition(): void
    {
        $suggestion = new ValueSuggestion('some_value', 'some_name', true);
        $this->tester->assertTrue($suggestion->isDisabled());
    }

    public function testJsonSerializes(): void
    {
        $suggestion = new ValueSuggestion('some_value', 'some_name');
        $this->tester->assertEquals(
            '{"name":"some_name","value":"some_value","disabled":false}',
            \json_encode($suggestion)
        );

        $suggestion = new ValueSuggestion('some_value', 'some_name', true);
        $this->tester->assertEquals(
            '{"name":"some_name","value":"some_value","disabled":true}',
            \json_encode($suggestion)
        );
    }
}
