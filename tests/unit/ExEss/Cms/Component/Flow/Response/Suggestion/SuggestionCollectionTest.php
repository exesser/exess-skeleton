<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\Component\Flow\Response\Suggestion;

use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Cms\Component\Flow\Response\Suggestion\ModelSuggestion;
use ExEss\Cms\Component\Flow\Response\Suggestion\SuggestionCollection;
use Helper\Testcase\UnitTestCase;
use JsonSerializable;

class SuggestionCollectionTest extends UnitTestCase
{
    public function testCanManipulateCollection(): void
    {
        // setup
        /** @var ModelSuggestion $suggestion1 */
        $suggestion1 = new ModelSuggestion(new Model(), 'suggestion1');
        /** @var ModelSuggestion $suggestion2 */
        $suggestion2 = new ModelSuggestion(new Model(), 'suggestion2');

        $collection = new SuggestionCollection();

        // assert for empty
        $this->tester->assertCount(0, $collection);
        $this->tester->assertInstanceOf(SuggestionCollection::class, $collection);
        $this->tester->assertInstanceOf(JsonSerializable::class, $collection);

        // add suggestions
        $collection[0] = $suggestion1;
        $collection[1] = $suggestion2;

        // assert added and content
        $this->tester->assertCount(2, $collection);
        $this->tester->assertEquals($suggestion1, $collection[0]);
        $this->tester->assertEquals($suggestion2, $collection[1]);
    }
}
