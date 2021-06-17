<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\FLW_Flows\Response;

use ExEss\Cms\FLW_Flows\Response\Model;
use Helper\Testcase\UnitTestCase;

class ModelTest extends UnitTestCase
{
    private Model $model;

    protected function _before(): void
    {
        $this->model = new Model;
    }

    public function testPropertyAssignment(): void
    {
        $this->model->randomProperty = 'randomValue';
        $this->tester->assertTrue(isset($this->model->randomProperty));
        $this->tester->assertSame('randomValue', $this->model->randomProperty);
        unset($this->model->randomProperty);
        $this->tester->assertFalse(isset($this->model->randomProperty));
    }

    public function testArrayAccess(): void
    {
        $this->model['randomProperty'] = 'randomValue';
        $this->tester->assertTrue(isset($this->model->randomProperty));
        $this->tester->assertSame('randomValue', $this->model['randomProperty']);
        unset($this->model['randomProperty']);
        $this->tester->assertFalse(isset($this->model['randomProperty']));
    }

    public function testConstructWithArray(): void
    {
        $model = new Model([
            'prop1' => 'val1',
            'prop2' => 'val2',
        ]);

        $this->tester->assertSame(2, \count($model));
        $this->tester->assertTrue(isset($model->prop1));
        $this->tester->assertSame('val1', $model->prop1);
        $this->tester->assertTrue(isset($model->prop2));
        $this->tester->assertSame('val2', $model->prop2);
    }

    public function testConstructWithStdClass(): void
    {
        $std = new \stdClass;
        $std->prop1 = 'val1';
        $std->prop2 = 'val2';
        $model = new Model($std);

        $this->tester->assertSame(2, \count($model));
        $this->tester->assertTrue(isset($model->prop1));
        $this->tester->assertSame('val1', $model->prop1);
        $this->tester->assertTrue(isset($model->prop2));
        $this->tester->assertSame('val2', $model->prop2);
    }

    public function testConstructWithSubModel(): void
    {
        $prop1 = new Model(['prop1_1' => 'val1_1']);
        $model = new Model([
            'prop1' => $prop1,
            'prop2' => 'val2',
        ]);

        $this->tester->assertSame(2, \count($model));
        $this->tester->assertTrue(isset($model->prop1));
        $this->tester->assertSame($prop1, $model->prop1);
        $this->tester->assertTrue(isset($model->prop2));
        $this->tester->assertSame('val2', $model->prop2);
    }

    public function testIterable(): void
    {
        $this->model->prop1 = 'val1';
        $this->model->prop2 = 'val2';

        $i = 1;
        foreach ($this->model as $key => $val) {
            $this->tester->assertSame("val$i", $this->model->{"prop$i"});
            $i++;
        }
        $this->tester->assertSame(3, $i);

        // twice to test rewind etc
        $i = 1;
        foreach ($this->model as $key => $val) {
            $this->tester->assertSame("prop$i", $key);
            $this->tester->assertSame("val$i", $val);
            $i++;
        }
        $this->tester->assertSame(3, $i);

        // twice to test rewind etc
        $i = 1;
        foreach ($this->model as $key => $val) {
            $this->tester->assertSame("prop$i", $key);
            $this->tester->assertSame("val$i", $val);
            $i++;
        }
        $this->tester->assertSame(3, $i);
    }

    /**
     * @dataProvider getFieldsFromModelDataProvider
     *
     * @param mixed $model
     */
    public function testGetFieldsFromModel($model, string $field, array $expectedResult): void
    {
        $model = new Model($model);

        // run test
        $result = $model->getFields($field);

        // assertions
        $this->tester->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider getFieldsFromModelDataProvider
     *
     * @param mixed $model
     */
    public function testGetFieldFromModel($model, string $field, array $expectedResult): void
    {
        $model = new Model($model);

        if (\count($expectedResult) > 1) {
            // Then
            $this->tester->expectThrowable(
                \UnexpectedValueException::class,
                function () use ($model, $field): void {
                    // When
                    $model->getField($field);
                }
            );
            return;
        }

        // run test
        $result = $model->getField($field);

        // assertions
        $this->tester->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getFieldsFromModelDataProvider
     * @dataProvider getFieldValueFromModelDataProvider
     *
     * @param mixed $model
     */
    public function testGetFieldValueFromModel(
        $model,
        string $field,
        array $expectedResult,
        ?string $default = null
    ): void {
        $model = new Model($model);

        if (\count($expectedResult) > 1) {
            // Then
            $this->tester->expectThrowable(
                \UnexpectedValueException::class,
                function () use ($model, $field, $default): void {
                    // When
                    $model->getFieldValue($field, $default);
                }
            );
            return;
        }

        // run test
        $result = $model->getFieldValue($field, $default);

        // assertions
        if (\count($expectedResult) === 0) {
            $this->tester->assertSame($default, $result);
        } else {
            $this->tester->assertSame(\array_values($expectedResult)[0], $result);
        }
    }

    public function getFieldValueFromModelDataProvider(): array
    {
        return [
            [
                [
                    "plop" => "aap",
                    "vanalles_|_1.3/0" => "someValue"
                ],
                'vanalles_|_1.3/0',
                ['vanalles_|_1.3/0' => 'someValue'],
            ],
            [
                [
                    "plop" => [
                        "app" => "bonobo",
                    ],
                    "vanalles_|_1.3/0" => "someValue"
                ],
                'plop.app',
                ['plop.app' => 'bonobo']
            ],
        ];
    }

    public function getFieldsFromModelDataProvider(): array
    {
        $data = [
            [
                ['test' => 'value'],
                'test',
                ['test' => 'value'],
            ],
            [
                [
                    'foo' => 'bar',
                    'test' => 'value',
                ],
                'test',
                ['test' => 'value'],
            ],
            [
                [
                    'test' => 'value',
                    'foo' => 'bar',
                ],
                'test',
                ['test' => 'value'],
            ],
            [
                [
                    'bar' => 'foo',
                    'test' => 'value',
                    'foo' => 'bar',
                ],
                'test',
                ['test' => 'value'],
            ],
            [
                ['module|submodule|test' => 'value'],
                'test',
                ['module|submodule|test' => 'value'],
            ],
            [
                [
                    'module|submodule|foo' => 'bar',
                    'module|submodule|test' => 'value',
                ],
                'test',
                ['module|submodule|test' => 'value'],
            ],
            [
                [
                    'module|submodule|test' => 'value',
                    'module|submodule|foo' => 'bar',
                ],
                'test',
                ['module|submodule|test' => 'value'],
            ],
            [
                [
                    'module|submodule|bar' => 'foo',
                    'module|submodule|test' => 'value',
                    'module|submodule|foo' => 'bar',
                ],
                'test',
                ['module|submodule|test' => 'value'],
            ],
            [
                [
                    'module|test|bar' => 'foo',
                    'module|submodule|test' => 'value',
                    'module|submodule|foo' => 'bar',
                ],
                'test',
                [
                    'module|submodule|test' => 'value',
                ],
            ],
            [
                [
                    'test|bar' => 'foo',
                    'module|submodule|test' => 'value',
                    'module|submodule|foo' => 'bar',
                ],
                'test',
                [
                    'module|submodule|test' => 'value',
                ],
            ],
            [
                [
                    'module|test|bar' => 'foo',
                    'module|submodule|test' => 'value',
                    'module|subtestmodule|foo' => 'bar',
                ],
                'test',
                [
                    'module|submodule|test' => 'value',
                ],
            ],
            [
                [
                    'module|test|bar' => 'foo',
                    'module|submodule|foo' => 'value',
                    'module|subtestmodule|foo' => 'bar',
                ],
                'test',
                [],
                'mydefault',
            ],
            // search for more specific property with pipe
            [
                [
                    'module|test|bar' => 'foo',
                    'module|test|submodule|bar' => 'foo',
                    'module|submodule|foo' => 'value',
                    'module|subtestmodule|foo' => 'bar',
                ],
                'test|bar',
                [
                    'module|test|bar' => 'foo',
                ],
            ],
            // allow for '_c' suffix to be dropped in search
            [
                [
                    'module|test|bar_c' => 'foo',
                    'module|submodule|foo' => 'value',
                    'module|subtestmodule|foo' => 'bar',
                ],
                'bar',
                [
                    'module|test|bar_c' => 'foo',
                ],
            ],
            [
                [
                    'module|test|bar' => 'foo',
                    'module|test|submodule|bar' => 'foo',
                    'module|submodule|test|bar' => 'value',
                    'module|subtestmodule|foo' => 'bar',
                ],
                'test|bar',
                [
                    'module|test|bar' => 'foo',
                    'module|submodule|test|bar' => 'value',
                ],
            ],
            [
                [
                    "parent" => [
                        'module|test|bar' => 'foo',
                        'module|test|submodule|bar' => 'foo',
                        'module|submodule|test|bar' => 'value',
                        'module|subtestmodule|foo' => 'bar',
                    ],
                ],
                'parent.bar',
                [
                    'parent.module|test|bar' => 'foo',
                    'parent.module|test|submodule|bar' => 'foo',
                    'parent.module|submodule|test|bar' => 'value',
                ],
            ],
            [
                [
                    "parent" => [
                        'module|test|bar' => 'foo',
                        'module|test|submodule|bar' => 'foo',
                        'module|submodule|test|bar' => 'value',
                        'module|subtestmodule|foo' => 'bar',
                    ],
                ],
                'parent.test|bar',
                [
                    'parent.module|test|bar' => 'foo',
                    'parent.module|submodule|test|bar' => 'value',
                ],
            ],
        ];

        // also try all these cases with stdClass instead of array
        foreach ($data as $params) {
            $params[0] = (object) $params[0];
            $data[] = $params;
        }

        return $data;
    }

    /**
     * @dataProvider findFieldValueFromModelDataProvider
     *
     * @param mixed $model
     * @param mixed $fields
     */
    public function testFindFieldValueFromModel($model, $fields, string $expectedResult, ?string $default = null): void
    {
        $model = new Model($model);

        // run test
        $result = $model->findFieldValue($fields, $default);

        // assertions
        if ($expectedResult) {
            $this->tester->assertSame($expectedResult, $result);
        } else {
            $this->tester->assertSame($default, $result);
        }
    }

    public function findFieldValueFromModelDataProvider(): array
    {
        return [
            [
                [
                    'accounts|company_number' => '123',
                    'dwp|company_number' => '123',
                ],
                'company_number',
                '123'
            ],
            [
                [
                    'accounts|company_number' => '123',
                    'dwp|company_number' => '456',
                ],
                'company_number',
                'default',
                'default'
            ],
            [
                [
                    'accounts|company_number' => '123',
                    'dwp|company_number' => '456',
                ],
                'accounts|company_number',
                '123',
            ],
            [
                [
                    'accounts|company_number' => '123',
                    'dwp|company_number' => '456',
                    'dwp|name' => 'test',
                    'dwp|vat_number' => '789',
                    'account|vat_number' => '789',
                ],
                [
                    'company_number',
                    'vat_number'
                ],
                '789',
            ],
        ];
    }

    public function testCanClearNamespaceOnObject(): void
    {
        $model = new Model();
        $model->{'some|namespace|property_1'} = 'value';
        $model->{'some|namespace|property_2'} = 'value';
        $model->{'some|other|namespace|property'} = 'value';

        // run test
        $model->clearNamespace('some|namespace');

        // assertions
        $this->tester->assertFalse(isset($model->{'some|namespace|property_1'}));
        $this->tester->assertFalse(isset($model->{'some|namespace|property_2'}));
        $this->tester->assertTrue(isset($model->{'some|other|namespace|property'}));
    }

    public function testGetCloneWithout(): void
    {
        $model = new Model();
        $model->setFieldValue('some|namespace|property', 'value');
        $model->setFieldValue('some|other|property', 'value');
        $model->setFieldValue('some|property', 'value');

        // run test
        $tempModel = $model->getCloneWithout(['some|namespace', 'some|other']);

        // assertions
        $this->tester->assertFalse($tempModel->hasField('some|namespace|property'));
        $this->tester->assertFalse($tempModel->hasField('some|other|property'));
        $this->tester->assertTrue($tempModel->hasField('some|property'));
    }

    public function testCanSetFieldValueToObject(): void
    {
        $key = 'some|key|to|be|found';
        $model = new Model();
        $model->{$key} = '';

        // run test
        $model->setFieldValue('found', 'some_value');

        // assertions
        $this->tester->assertEquals('some_value', $model->{$key});
        $this->tester->assertEquals(['some|key|to|be|found' => 'some_value'], $model->toArray());
    }

    public function testSetFieldValueWhenFieldNotFoundAndStrictIsFalse(): void
    {
        $model = new Model();

        // run test
        $model->setFieldValue('doesntexist', 'value', false);

        // assertions
        $this->tester->assertEquals(['doesntexist' => 'value'], $model->toArray());
    }

    public function testSetFieldValueExceptionIsThrownWhenFieldIsNotFoundAndStrictIsTrue(): void
    {
        // Given
        $model = new Model();

        // Then
        $this->tester->expectThrowable(
            new \DomainException('Field doesntexist was not found on the model'),
            function () use ($model): void {
                // When
                $model->setFieldValue('doesntexist', 'value', true);
            }
        );
    }

    /**
     * @dataProvider hasNonEmptyValueForDataProvider
     *
     * @param mixed $value
     */
    public function testHasNonEmptyValueFor($value, bool $expectedResult): void
    {
        $model = new Model();
        $model->keyToSearch = $value;

        // run tests
        $result = $model->hasNonEmptyValueFor('keyToSearch');

        // assertions
        $this->tester->assertSame($expectedResult, $result);
    }

    public function hasNonEmptyValueForDataProvider(): array
    {
        return [
            // empty values
            'Zero' => [0, false],
            'null' => [null, false],
            'empty string' => ['', false],
            'empty array' => [[], false],
            'empty model' => [new Model(), false],
            // non empty values
            '123' => [123, true],
            'string ' => ['foo-bar', true],
            'array' => [['foo' => 'bar'], true],
            'model' => [new Model(['foo' => 'bar']), true],
        ];
    }

    public function testHasNonEmptyValueForDuplicateKey(): void
    {
        $model = new Model(['foo' => new Model(["foo1" => "bar1"]), 'dwp|foo' => 'dwp bar']);

        // assertions
        $this->tester->assertTrue($model->hasNonEmptyValueFor('foo'));
    }

    /**
     * @dataProvider hasValueForProvider
     *
     * @param mixed $value
     */
    public function testHasValueFor($value, bool $result): void
    {
        $model = new Model(['fieldToFind' => $value]);
        $this->tester->assertEquals($result, $model->hasValueFor('fieldToFind'));
    }

    public function hasValueForProvider(): array
    {
        return [
            "Zero should count as value" => [0, true],
            "String zero should count as value" => ['0', true],
            "False should count as value" => [false, true],
            "Null should not count as value" => [null, false],
            "Empty string should not count as value" => ['', false],
            "Empty array should not count as value" => [[], false]
        ];
    }

    public function testHasValueForNotExistingField(): void
    {
        $this->tester->assertFalse((new Model())->hasValueFor('some-field'));
    }

    public function testClone(): void
    {
        // setup
        $this->model->foo = 'bar';
        $this->model->child = ['foo' => 'bar'];

        // run test
        $clone = clone $this->model;

        // asserts
        $this->tester->assertSame($clone->toArray(), $this->model->toArray());
        $this->tester->assertNotSame($clone, $this->model);
        $this->tester->assertNotSame($clone->child, $this->model->child);
    }

    public function testcanGetNamespaceOnObject(): void
    {
        $model = new Model();
        $model->{'aos_products_quotes'} = [
            'quote-1' => [
                'gln_account|aos_quotes|aos_products_quotes|switchtype_c' => 'CUSTOMER_SWITCH_NOTIFICATION',
            ],
        ];
        $model->{'aos_products_quotes|gln_account|aos_quotes|aos_products_quotes|anotherfield'} = 'value';
        $model->{'some|other|namespace|property'} = 'value';

        // run test
        $children = $model->getNamespace('aos_products_quotes');

        // assertions
        $this->tester->assertTrue(isset($children['gln_account|aos_quotes|aos_products_quotes|anotherfield']));
        $this->tester->assertFalse(isset($children['gln_account|aos_quotes|anotherfield']));
        $this->tester->assertFalse(
            isset($children['aos_products_quotes|gln_account|aos_quotes|aos_products_quotes|anotherfield'])
        );
    }

    /**
     * @dataProvider getFieldKeyFromModelDataProvider
     *
     * @param mixed $model
     */
    public function testGetFieldKeyFromModel($model, string $field, ?string $expectedResult): void
    {
        // setup
        $model = new Model($model);

        // run & assert
        if ($expectedResult === null) {
            $this->tester->expectThrowable(
                \UnexpectedValueException::class,
                function () use ($model, $field): void {
                    $model->getFieldKey($field);
                }
            );
        } else {
            $this->tester->assertSame($expectedResult, $model->getFieldKey($field));
        }
    }

    public function getFieldKeyFromModelDataProvider(): array
    {
        $data = [
            [
                ['test' => 'value'],
                'test',
                'test',
            ],
            [
                ['module|submodule|test' => 'value'],
                'test',
                'module|submodule|test',
            ],
            [
                ['foo' => 'bar'],
                'test',
                null,
            ],
            [
                ['module|submodule|foo' => 'bar'],
                'test',
                null,
            ],
        ];

        // also try all these cases with stdClass instead of array
        foreach ($data as $params) {
            $params[0] = (object) $params[0];
            $data[] = $params;
        }

        return $data;
    }

    /**
     * @dataProvider substituteFieldKeyFromModelDataProvider
     *
     * @param mixed $model
     */
    public function testSubstituteFieldKeyFromModel(
        $model,
        string $field,
        string $substition,
        ?string $expectedResult
    ): void {
        // setup
        $model = new Model($model);

        // run & assert
        if ($expectedResult === null) {
            $this->tester->expectThrowable(
                \UnexpectedValueException::class,
                function () use ($model, $field, $substition): void {
                    $model->substituteFieldKey($field, $substition);
                }
            );
        } else {
            $this->tester->assertSame($expectedResult, $model->substituteFieldKey($field, $substition));
        }
    }

    public function substituteFieldKeyFromModelDataProvider(): array
    {
        $data = [
            [
                ['test' => 'value'],
                'test',
                'foo',
                'foo',
            ],
            [
                ['module|submodule|test' => 'value'],
                'test',
                'foo',
                'module|submodule|foo',
            ],
            [
                ['foo' => 'bar'],
                'test',
                'foo',
                null,
            ],
            [
                ['module|submodule|foo' => 'bar'],
                'test',
                'foo',
                null,
            ],
        ];

        // also try all these cases with stdClass instead of array
        foreach ($data as $params) {
            $params[0] = (object) $params[0];
            $data[] = $params;
        }

        return $data;
    }

    /**
     * @dataProvider findFirstKeyFromModelDataProvider
     *
     * @param mixed $model
     */
    public function testFindFirstKeyFromModel($model, array $fields, string $expectedResult, bool $exactMatch): void
    {
        // setup
        $model = new Model($model);

        // run & assert
        if ($exactMatch === false && $expectedResult === '') {
            $this->tester->expectThrowable(
                \UnexpectedValueException::class,
                function () use ($model, $fields, $expectedResult, $exactMatch): void {
                    $this->tester->assertSame($expectedResult, $model->findFirstKey($fields, '', $exactMatch));
                }
            );
        } else {
            $this->tester->assertSame($expectedResult, $model->findFirstKey($fields, '', $exactMatch));
        }
    }

    public function findFirstKeyFromModelDataProvider(): array
    {
        $data = [
            [
                ['test' => 'value'],
                ['test'],
                'test',
                true,
            ],
            [
                ['test' => 'value'],
                ['test'],
                'test',
                false,
            ],
            [
                ['foo|test' => 'value', 'test' => 'value'],
                ['test'],
                'test',
                true,
            ],
            [
                ['foo|test' => 'value', 'test' => 'value'],
                ['foo|test'],
                'foo|test',
                true,
            ],
            [
                ['foo|test' => 'value', 'test' => 'value'],
                ['foo|test'],
                'foo|test',
                false,
            ],
            [
                ['foo|test' => 'value', 'test' => 'value'],
                ['test'],
                '',
                false,
            ],
            [
                ['foo|test' => 'value', 'test' => 'value'],
                ['foo'],
                '',
                true,
            ],
        ];

        // also try all these cases with stdClass instead of array
        foreach ($data as $params) {
            $params[0] = (object) $params[0];
            $data[] = $params;
        }

        return $data;
    }
}
