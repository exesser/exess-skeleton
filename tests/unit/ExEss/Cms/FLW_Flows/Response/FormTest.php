<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\FLW_Flows\Response;

use Helper\Testcase\UnitTestCase;
use ExEss\Cms\FLW_Flows\Response\Form\ToggleField;
use ExEss\Cms\FLW_Flows\Response\Form;

class FormTest extends UnitTestCase
{
    /**
     * @dataProvider getCardForProvider
     */
    public function testCanGetCardFor(array $groups, string $name, ?string $result): void
    {
        $form = new Form('foo', 'foo', 'foo', 'foo');
        foreach ($groups as $card => $fields) {
            $form->setGroup($card, $fields);
        }

        $this->tester->assertEquals($result, $form->getCardFor($name));
    }

    public function getCardForProvider(): array
    {
        $field = new \stdClass();
        $field->id = 'field1';

        return [
            [['r1c1' => [$field]], 'field1', 'r1c1'],
            [['r1c1' => [], 'r2c3' => [$field]], 'field1', 'r2c3'],
            [['r1c1' => [$field], 'r2c3' => []], 'field1', 'r1c1'],
            [['r1c1' => [], 'r2c3' => []], 'field1', null],
            [['r1c1' => []], 'field1', null],
        ];
    }

    /**
     * @dataProvider getHasCardProvider
     */
    public function testCanHasCard(array $groups, string $findCard, bool $result): void
    {
        $form = new Form('foo', 'foo', 'foo', 'foo');
        foreach ($groups as $card => $fields) {
            $form->setGroup($card, $fields);
        }

        $this->tester->assertEquals($result, $form->hasCard($findCard));
    }

    public function getHasCardProvider(): array
    {
        return [
            [['r1c1' => []], 'r1c1', true],
            [['r1c1' => []], 'r2c2', false],
            [['r1c1' => [], 'r2c2' => []], 'r2c2', true],
            [['r1c1' => [], 'r2c2' => []], 'r1c1', true],
            [['r2c2' => [], 'r1c1' => []], 'r1c1', true],
            [['r2c2' => [], 'r1c1' => []], 'r3c1', false],
        ];
    }

    public function testCanAddCard(): void
    {
        $form = new Form('foo', 'foo', 'foo', 'foo');

        $form->addCard('r2c2');
        $this->assertCard($form, 'r2c2');

        $form = new Form('foo', 'foo', 'foo', 'foo');
        $form->setGroup('r2c2', []);

        $form->addCard('r2c2');
        $this->assertCard($form, 'r2c2');
    }

    /**
     * @dataProvider getHasFieldProvider
     */
    public function testCanHasField(array $groups, string $field, bool $result): void
    {
        $form = new Form('foo', 'foo', 'foo', 'foo');
        foreach ($groups as $card => $fields) {
            $form->setGroup($card, $fields);
        }

        $this->tester->assertEquals($result, $form->hasField($field));
    }

    public function getHasFieldProvider(): array
    {
        $field = new \stdClass();
        $field->id = 'field1';

        return [
            [['r1c1' => [$field]], 'field1', true],
            [['r1c1' => [$field]], 'field2', false],
            [['r2c2' => [$field]], 'field1', true],
        ];
    }

    public function testCanAddField(): void
    {
        $form = new Form('foo', 'foo', 'foo', 'foo');
        $field = new ToggleField('field1', 'Some Field');

        $form->addField('r1c1', $field);

        $this->assertCard($form, 'r1c1');
        $this->tester->assertContains($field, $form->getGroup('r1c1')->fields);

        $form = new Form('foo', 'foo', 'foo', 'foo');
        $form->setGroup('r1c1', []);

        $field = new \stdClass();
        $form->addField('r1c1', $field);
        $this->tester->assertContains($field, $form->getGroup('r1c1')->fields);
    }

    public function testCanRemoveField(): void
    {
        $field = new ToggleField('field1', 'Some Field');

        $form = new Form('foo', 'foo', 'foo', 'foo');
        $form->setGroup('r1c1', [$field]);

        $form->removeField('field1');
        $this->tester->assertCount(0, $form->getGroup('r1c1')->fields);
    }

    public function testCanGetField(): void
    {
        $field = new ToggleField('field1', 'Some Field');

        $form = new Form('foo', 'foo', 'foo', 'foo');
        $form->setGroup('r1c1', [$field]);

        $this->tester->assertEquals($field, $form->getField('field1'));
    }

    public function testCanGetFields(): void
    {
        $field1 = new \stdClass();
        $field2 = new \stdClass();

        $form = new Form('foo', 'foo', 'foo', 'foo');
        $form->setGroup('r1c1', [$field1]);
        $form->setGroup('r1c2', [$field2]);

        $fields = $form->getFields();

        $this->tester->assertIsArray($fields, 'Returns an array of fields');
        $this->tester->assertCount(2, $fields, 'Should contain two fields');
        $this->tester->assertContains($field1, $fields, 'Should contain field 1 r1c1');
        $this->tester->assertContains($field2, $fields, 'Should contain field 2 r1c2');
    }

    /**
     * Asserts the given card-name is a card in the form
     */
    private function assertCard(Form $form, string $card): void
    {
        $this->tester->assertIsArray($form->getGroups());
        $this->tester->assertIsObject($form->getGroup($card));
        $this->tester->assertIsArray($form->getGroup($card)->fields);
    }
}
