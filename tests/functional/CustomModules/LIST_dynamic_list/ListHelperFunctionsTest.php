<?php declare(strict_types=1);

namespace Test\Functional\CustomModules\LIST_dynamic_list;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Dictionary\Format;
use ExEss\Cms\Doctrine\Type\FlowType;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use ExEss\Cms\Doctrine\Type\UserStatus;
use ExEss\Cms\Entity\ConditionalMessage;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\ListCellLink;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Exception\ConfigInvalidException;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\ListFunctions\HelperClasses\ListHelperFunctions;
use ExEss\Cms\Parser\ExpressionGroup;
use ExEss\Cms\Parser\ExpressionParserOptions;
use ExEss\Cms\Parser\PathResolverOptions;
use Helper\Testcase\FunctionalTestCase;

class ListHelperFunctionsTest extends FunctionalTestCase
{
    protected ListHelperFunctions $listFunctions;
    private EntityManager $em;

    public function _before(): void
    {
        $this->em = $this->tester->grabService('doctrine.orm.entity_manager');
        $this->listFunctions = $this->tester->grabService(ListHelperFunctions::class);
    }

    public function testParseWithInStatement(): void
    {
        // Given
        $listId = $this->tester->generateDynamicList();
        $this->tester->generateListLinkCell($listId, ['order_c' => 10]);
        $this->tester->generateListLinkCell($listId, ['order_c' => 20]);

        // When
        $response = $this->listFunctions->parseListValue(
            $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]),
            '%cellLinks{where:order in (\'20\', \'30\')}|order%'
        );

        // Then
        $this->tester->assertEquals(['20'], $response);
    }

    public function testParseDateOnExternal(): void
    {
        // Given
        $mapper = new \JsonMapper();
        $mapper->bIgnoreVisibility = true;

        $object = $mapper->map((object) [
            "effectiveDate" => new \DateTime("2020-01-01 10:25"),
            "creationDateTime" => new \DateTime("2020-02-28"),
        ], new TestObject());

        // When
        $response = $this->listFunctions->parseListValue(
            new ExpressionParserOptions($object),
            '%effectiveDate% - %creationDateTime%'
        );

        // Then
        $this->tester->assertEquals('01-01-2020 10:25 - 28-02-2020', $response);
    }

    public function neqStatementProvider(): array
    {
        return [
            [
                '%cellLinks{where:order<>20}|order%',
                ['10'],
            ],
            [
                '%cellLinks(order<>10;order<>20)|order%',
                '',
            ],
            [
                '%cellLinks(order<>30){order:order DESC}|order%',
                ['20', '10'],
            ],
            [
                '%cellLinks(order<>30;order<>20){order:order DESC}|order%',
                ['10'],
            ],
        ];
    }

    /**
     * @dataProvider neqStatementProvider
     * @param mixed $expectedResult
     */
    public function testParseWithNeqStatement(string $expression, $expectedResult): void
    {
        // Given
        $listId = $this->tester->generateDynamicList();
        $this->tester->generateListLinkCell($listId, ['order_c' => 10]);
        $this->tester->generateListLinkCell($listId, ['order_c' => 20]);

        // When
        $result = $this->listFunctions->parseListValue(
            $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]),
            $expression
        );

        // Then
        $this->tester->assertEquals($expectedResult, $result);
    }

    public function testParseListQuery(): void
    {
        // Given
        $listId = $this->tester->generateDynamicList();
        $linkCellId1 = $this->tester->generateListLinkCell($listId, ['order_c' => 10]);
        $linkCellId2 = $this->tester->generateListLinkCell($listId, [
            'order_c' => 20,
            'cell_id' => $this->tester->generateListCell(['line1' => 'test']),
        ]);

        $resolverOptions = new PathResolverOptions();
        $resolverOptions->setAllBeans([['id' => $listId]]);

        // When
        $response = $this->listFunctions->parseListQuery(
            $this->em->getClassMetadata(ListDynamic::class),
            new ExpressionGroup('
                %cellLinks(order<>\'FOO\';order<>\'BAR\'){order:order DESC}|id%
                %cellLinks(order<>\'FOO\';order<>\'BAR\'){order:order DESC}|order%
                %cellLinks(order<>\'FOO\';order<>\'BAR\'){order:order DESC}|cell|line1%
            '),
            $resolverOptions
        );

        // Then
        $this->tester->assertCount(1, $response);
        $this->tester->assertInstanceOf(ListDynamic::class, $response[0]);
        $this->tester->assertCount(2, $response[0]->getCellLinks());
    }

    public function testParseWithEnumKey(): void
    {
        // Given
        $flow = new Flow();
        $flow->setType(FlowType::DASHBOARD);

        $expression = '%{key}type{/key}%';
        $expectedValue = FlowType::DASHBOARD;

        // When
        $parserOptions = (new ExpressionParserOptions($flow))
            ->setReplaceEnumValueWithLabel(true)
        ;

        // Then
        $this->tester->assertSame(
            $expectedValue,
            $this->listFunctions->parseListValue($parserOptions, $expression)
        );

        // When
        $parserOptions = (new ExpressionParserOptions($flow));

        // Then
        $this->tester->assertSame(
            $expectedValue,
            $this->listFunctions->parseListValue($parserOptions, $expression)
        );
    }

    /**
     * @dataProvider dataProviderParseListValueWithObject
     */
    public function testParseWithEnumValue(?string $language, string $expectedValue): void
    {
        // Given
        $raw = FlowType::DASHBOARD;

        if ($language) {
            $this->tester->generateTranslation([
                'name' => $raw,
                'translation' => 'translated value',
                'locale' => $language,
                'domain' => TranslationDomain::GUIDANCE_ENUM,
                'description' => (new FlowType)->getName() ,
            ]);
        }

        /** @var Flow $flow */
        $flow = $this->tester->grabEntityFromRepository(
            Flow::class,
            ['id' => $this->tester->generateFlow(['type_c' => $raw])]
        );

        $parserOptions = (new ExpressionParserOptions($flow))
            ->setLanguage($language)
            ->setReplaceEnumValueWithLabel(true)
        ;

        // When
        $result = $this->listFunctions->parseListValue($parserOptions, '%type%');

        // Then
        // @todo re-enable when translations use entities
        // $this->tester->assertSame($expectedValue, $result);
    }

    public function dataProviderParseListValueWithObject(): array
    {
        return [
            'with-language' => ['nl_BE', 'translated value'],
            'without-language' => [null, 'Dashboard'],
        ];
    }

    /**
     * @dataProvider objectProvider
     */
    public function testParseSecurity(?\stdClass $baseFatEntity = null): void
    {
        $this->tester->assertSame(
            '1',
            $this->listFunctions->parseListValue($baseFatEntity, '%current_user_id%'),
            'Assert user id is correct'
        );
    }

    /**
     * @dataProvider objectProvider
     */
    public function testParseNow(?\stdClass $baseFatEntity = null): void
    {
        $this->tester->assertSame(
            (new \DateTime())->format('Y-m-d'),
            $this->listFunctions->parseListValue($baseFatEntity, '%TODAY%'),
            'Assert current date is correct'
        );
        $this->tester->assertSame(
            (new \DateTime())->format('Y-m-d H:i:s'),
            $this->listFunctions->parseListValue($baseFatEntity, '%NOW%'),
            'Assert current date and time is correct'
        );
    }

    public function objectProvider(): array
    {
        return [
            [null],
            [new \stdClass()],
        ];
    }

    public function testParseListValue(): void
    {
        $listId = $this->tester->generateDynamicList([
            'name' => 'list-name',
        ]);
        $linkCellId1 = $this->tester->generateListLinkCell($listId, [
            'name' => 'link-name',
            'description' => '%value%',
        ]);

        /** @var ListDynamic $list */
        $list = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]);
        /** @var ListCellLink $linkCell */
        $linkCell = $this->tester->grabEntityFromRepository(ListCellLink::class, ['id' => $linkCellId1]);

        // When / Then
        $this->tester->assertEquals(
            $linkCell->getName(),
            $this->listFunctions->parseListValue($linkCell, '%name%')
        );

        $this->tester->assertEquals(
            $list->getName(),
            $this->listFunctions->parseListValue($linkCell, '%list|name%')
        );

        $this->tester->assertEquals(
            $list->getDescription(),
            $this->listFunctions->parseListValue($linkCell, '%list|description%')
        );

        $this->tester->assertEquals(
            [$linkCell->getName()],
            $this->listFunctions->parseListValue($list, "%cellLinks(name='link-name')|name%")
        );

        $this->tester->assertEquals(
            '',
            $this->listFunctions->parseListValue($list, "%cellLinks(name='b')|name%")
        );

        $this->tester->assertEquals(
            [$linkCell->getName()],
            $this->listFunctions->parseListValue($list, "%cellLinks(description='\%value\%')|name%")
        );

        $this->tester->assertEquals(
            "foo not like '%BAR%' and so on",
            $this->listFunctions->parseListValue($list, "foo not like '\%BAR\%' and so on")
        );
    }

    /**
     * @dataProvider multipleReturnDataProvider
     * @param mixed $expectedReturn
     */
    public function testParseWithBeanWithMultipleReturn(string $toBeParsedString, $expectedReturn): void
    {
        // Given
        $listId = $this->tester->generateDynamicList();
        $this->tester->generateListCellForList($listId, ['line1' => 'cell 1'], ['order_c' => 10]);
        $this->tester->generateListCellForList($listId, ['line1' => 'cell 1'], ['name' => 'a', 'order_c' => 20]);
        $this->tester->generateListCellForList($listId, ['line1' => 'cell 2'], ['order_c' => 30]);
        $this->tester->generateListCellForList($listId, ['line1' => 'cell 2'], ['order_c' => 10]);
        $this->tester->generateListCellForList($listId, ['line1' => 'cell 3'], ['name' => 'b', 'order_c' => 20]);
        $this->tester->generateListCellForList($listId, ['line1' => 'cell 3'], ['order_c' => 30]);

        // When
        $returnValue = $this->listFunctions->parseListValue(
            $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]),
            $toBeParsedString
        );

        // Then
        $this->tester->assertSame($expectedReturn, $returnValue);
    }

    public function multipleReturnDataProvider(): array
    {
        return [
            [
                '%cellLinks{order:order DESC}[]|order%',
                ['30', '30', '20', '20', '10', '10']
            ],[
                '%cellLinks{where:order="20"}[]|order%',
                ['20', '20']
            ],[
                '%cellLinks(order="20")[]|order%',
                ['20', '20']
            ],[
                '%cellLinks(order="20"){order:name ASC}[]|cell|line1%',
                ['cell 1', 'cell 3']
            ],
        ];
    }

    public function testGetDynamicListRowCellParamsWithCorrectParams(): void
    {
        $entity = new ListDynamic();
        $entity->setId($id = $this->tester->generateUuid());
        $entity->setName($name = $this->tester->generateUuid());

        $line = <<<JSON
{
    "mainMenuKey":"sales",
    "dashboardId":"account",
    "recordId":"%id% - %name%",
    "flowId":"CUPQ"
}
JSON;

        $expectedResult = <<<JSON
{
    "mainMenuKey":"sales",
    "dashboardId":"account",
    "recordId":"$id - $name",
    "flowId":"CUPQ"
}
JSON;

        // run test
        $result = $this->listFunctions->parseListValue($entity, $line);

        // assertions
        $this->tester->assertEquals($expectedResult, $result);
    }

    public function testGetDynamicListRowCellParamsWithMissingParams(): void
    {
        $entity = new ListDynamic();
        $entity->setId($id = $this->tester->generateUuid());

        $line = <<<JSON
{
    "mainMenuKey":"sales",
    "dashboardId":"account",
    "recordId":"%id% - %name%",
    "flowId":"CUPQ"
}
JSON;

        $expectedResult = <<<JSON
{
    "mainMenuKey":"sales",
    "dashboardId":"account",
    "recordId":"$id - ",
    "flowId":"CUPQ"
}
JSON;

        // run test
        $result = $this->listFunctions->parseListValue($entity, $line);

        // assertions
        $this->tester->assertEquals($expectedResult, $result);
    }

    public function testGetDynamicListRowCellParamsWithEmptyParams(): void
    {
        $entity = new ListDynamic();
        $entity->setId($this->tester->generateUuid());

        $line = '';

        $expectedResult = '';

        // run test
        $result = $this->listFunctions->parseListValue($entity, $line);

        // assertions
        $this->tester->assertEquals($expectedResult, $result);
    }

    public function testGetRelatedIds(): void
    {
        // Given
        $listId = $this->tester->generateDynamicList();
        $linkCellId1 = $this->tester->generateListLinkCell($listId, ['order_c' => 10]);
        $linkCellId2 = $this->tester->generateListLinkCell($listId, ['order_c' => 20]);

        $entity = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]);

        $line = '%rel|cellLinks%';

        // When
        $result = $this->listFunctions->parseListValue($entity, $line);

        // Then
        $this->tester->assertIsArray($result);
        $this->tester->assertCount(2, $result);
        $this->tester->assertContains($linkCellId1, $result);
        $this->tester->assertContains($linkCellId2, $result);
    }

    /**
     * @dataProvider ifStatementProvider
     * @param mixed $expected
     */
    public function testIfStatement(string $expression, $expected, bool $expectExceptionOnEmpty = true): void
    {
        $model = new Model([
            'ean_c' => 'value-ean-c',
            'product_type_c' => 'GAS',
            'dwp|elec_contract_line_id' => 'value-elec-contract-line-id',
            'dwp|gas_contract_line_id' => 'value-gas-contract-line-id',
            'dwp|test' => true,
            'dwp|result' => true,
            'dwp|something' => null,
        ]);

        // run test with filled model
        $this->tester->assertEquals(
            $expected,
            $this->listFunctions->parseListValue($model, $expression),
            "Expect value '$expected' for expression '$expression'"
        );

        if ($expectExceptionOnEmpty) {
            $this->tester->expectThrowable(
                ConfigInvalidException::class,
                function () use ($expression): void {
                    // run test with empty model
                    $this->listFunctions->parseListValue(new Model(), $expression);
                }
            );
        } else {
            $this->tester->assertEquals(
                $expected,
                $this->listFunctions->parseListValue(new Model(), $expression),
                "Expect value '$expected' for expression '$expression'"
            );
        }
    }

    // @codingStandardsIgnoreStart
    public function ifStatementProvider()
    {
        return [
            'Can handle expressions with a model' => [
                "{if}'%product_type_c%'==='ELEC';%dwp|elec_contract_line_id%;%dwp|gas_contract_line_id%{/if}",
                'value-gas-contract-line-id'
            ],
            'Can handle nested ifs with an expression on a model' => [
                "{if}'%product_type_c%'==='ELEC';%dwp|elec_contract_line_id%;{if}'%product_type_c%'==='GAS';%dwp|gas_contract_line_id%{/if}{/if}",
                'value-gas-contract-line-id'
            ],
            'Can handle serial ifs with an expression on a model' => [
                "{if}'%product_type_c%'==='ELEC';%dwp|elec_contract_line_id%{/if}{if}'%product_type_c%'==='GAS';%dwp|gas_contract_line_id%{/if}",
                'value-gas-contract-line-id'
            ],
            'Can work with booleans' => [
                '{if}%dwp|test% === true;false;%dwp|result%{/if}',
                false
            ],
            'Can work with booleans with boolean result' => [
                '{if}%dwp|test% !== true;false;%dwp|result%{/if}',
                true
            ],
            'Can work with null values' => [
                '{if}%dwp|test% === true;null;%dwp|something%{/if}',
                null
            ],
            'Can work with null values with null result' => [
                '{if}%dwp|test% !== true;123;%dwp|something%{/if}',
                null
            ],
            'Can compare against null value with null result' => [
                '{if}%dwp|something% !== null;123;%dwp|something%{/if}',
                null
            ],
            'Can evaluate conjunction' => [
                '{if}true && false;true;AND{/if}',
                'AND',
                false,
            ],
            'Can evaluate disjunction' => [
                '{if}false || true;OR;false{/if}',
                'OR',
                false,
            ],
        ];
    }
    // @codingStandardsIgnoreEnd

    public function testResolveFunctionWithEmptyExpression(): void
    {
        $this->tester->expectThrowable(
            ConfigInvalidException::class,
            function (): void {
                $this->listFunctions->parseListValue(
                    new Model(),
                    "{if}%dwp|someNonExistingField% == 'somevalue';true;false{/if}"
                );
            }
        );
    }

    public function testInvalidOrderBy(): void
    {
        // Given
        $listId = $this->tester->generateDynamicList();
        $expr = '%createdBy|userGroups{order:noexist DESC}|name%';

        // Then
        $this->tester->expectThrowable(
            \Throwable::class,
            function () use ($listId, $expr): void {
                // When
                $this->listFunctions->parseListValue(
                    $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]),
                    $expr
                );
            }
        );
    }

    public function testUserPrimaryGroup(): void
    {
        // Given
        $securityGroupId = $this->tester->generateSecurityGroup('test');

        $userId = $this->tester->generateUser('wky', [
            'first_name' => 'B',
            'last_name' => 'D',
            'status' => UserStatus::ACTIVE,
        ]);
        $this->tester->linkUserToSecurityGroup($userId, $securityGroupId, ['primary_group' => 1]);
        $this->tester->linkUserToSecurityGroup(
            $userId,
            $this->tester->generateSecurityGroup('test 2')
        );
        $this->tester->linkUserToSecurityGroup(
            $userId,
            $this->tester->generateSecurityGroup('test 3')
        );

        // When
        $result = $this->listFunctions->parseListValue(
            $this->tester->grabEntityFromRepository(User::class, ['id' => $userId]),
            '%userGroups(primaryGroup=true)[]|securityGroup|id%'
        );

        // Then
        $this->tester->assertEquals([$securityGroupId], $result);
    }

    public function testEntitySecurityGroup(): void
    {
        // Given
        $securityGroupId = $this->tester->generateSecurityGroup('test');

        $messageId = $this->tester->generateConditionalMessage();
        $this->tester->linkSecurityGroupConditionalMessage($messageId, $securityGroupId);

        // When
        $result = $this->listFunctions->parseListValue(
            $this->tester->grabEntityFromRepository(ConditionalMessage::class, ['id' => $messageId]),
            '%securityGroups[]|id%'
        );

        // Then
        $this->tester->assertEquals([$securityGroupId], $result);
    }

    public function objectNameProvider(): array
    {
        // Given
        return [
            [new ListDynamic(), ListDynamic::class],
            [null, ''],
        ];
    }

    /**
     * @param mixed $baseEntity
     * @dataProvider objectNameProvider
     */
    public function testObjectName($baseEntity, string $expectedResult): void
    {
        // When
        $result = $this->listFunctions->parseListValue($baseEntity, '%object_name%');

        // Then
        $this->tester->assertEquals($expectedResult, $result);
    }

    public function entityProvider(): array
    {
        return [
            [
                '%title%',
                "some title",
            ],
            [
                '%createdBy|id%',
                "1",
            ],
            [
                '%cellLinks[]|order%',
                ['2000', '1000'],
            ],
            [
                '%cellLinks{order:order ASC}[]|order%',
                ['1000', '2000'],
            ],
            [
                '%cellLinks[]|cell|line1%',
                ['cell 2', 'cell 1'],
            ],
            [
                '%cellLinks[]|cell|createdBy|id%',
                ['1', '1'],
            ],
            [
                '%cellLinks{where:order=1000}[]|cell|line1%',
                ['cell 1'],
            ],
        ];
    }

    /**
     * @param mixed $expected
     * @dataProvider entityProvider
     */
    public function testWithEntity(string $expression, $expected): void
    {
        // Given
        $listId = $this->tester->generateDynamicList([
            'created_by' => '1',
            'date_entered' => \date(Format::DB_DATETIME_FORMAT),
            'title' => "some title",
        ]);
        $this->tester->generateListLinkCell($listId, [
            'date_entered' => \date('Y-m-d H:i') . ':01',
            'order_c' => 1000,
            'cell_id' => $this->tester->generateListCell([
                'line1' => 'cell 1',
            ]),
        ]);
        $this->tester->generateListLinkCell($listId, [
            'date_entered' => \date('Y-m-d H:i') . ':02',
            'order_c' => 2000,
            'cell_id' => $this->tester->generateListCell([
                'line1' => 'cell 2',
            ]),
        ]);

        $entity = $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]);

        // When
        $result = $this->listFunctions->parseListValue($entity, $expression);

        // Then
        $this->tester->assertEquals($expected, $result);
    }
}
