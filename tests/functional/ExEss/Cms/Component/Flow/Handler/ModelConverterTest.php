<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Component\Flow\Handler;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\Entity\User;
use ExEss\Cms\Component\Flow\Handler\FlowData;
use ExEss\Cms\Component\Flow\Handler\ModelConverter;
use ExEss\Cms\Component\Flow\Response\Model;
use Helper\Testcase\FunctionalTestCase;

class ModelConverterTest extends FunctionalTestCase
{
    private ModelConverter $modelConverter;

    public function _before(): void
    {
        $this->modelConverter = $this->tester->grabService(ModelConverter::class);
    }

    /**
     * @dataProvider explodeModelDataProvider
     */
    public function testExplodeModel(array $model, array $expectedResult): void
    {
        // Given
        $flow = new Flow;
        $flow->setBaseObject('something');
        $flow->setExternal(false);

        $data = new FlowData($flow, new Model($model));

        // When
        $this->modelConverter->handle($data);

        // Then
        $this->tester->assertEquals($expectedResult, $data->getConvertedModel());
    }

    public function explodeModelDataProvider(): array
    {
        return [
            [
                [
                    'foo' => 'bar',
                    'baseModule' => User::class,
                ],
                [
                    User::class => [
                        'foo' => 'bar'
                    ],
                    'baseModule' => User::class,
                ],
            ],
            [
                [
                    'foo' => 'bar',
                    'relation|prop' => 'foobar',
                    'baseModule' => User::class,
                ],
                [
                    User::class => [
                        'foo' => 'bar',
                    ],
                    'relation' => [
                        'prop' => 'foobar',
                    ],
                    'baseModule' => User::class,
                ],
            ],
            [
                [
                    'foo' => 'bar',
                    'relation|prop' => 'foobar',
                    'relation|subrelation|subprop' => 'barfoo',
                    'baseModule' => User::class,
                ],
                [
                    User::class => [
                        'foo' => 'bar'
                    ],
                    'relation' => [
                        'prop' => 'foobar',
                        'subrelation' => [
                            'subprop' => 'barfoo',
                        ],
                    ],
                    'baseModule' => User::class,
                ],
            ],
            [
                [
                    'foo' => 'bar',
                    'relation|prop' => 'foobar',
                    'relation|subrelation|subprop' => 'barfoo',
                    "relation2|subrelation(type='test')|subprop" => 'barfoo',
                    'baseModule' => User::class,
                ],
                [
                    User::class => [
                        'foo' => 'bar',
                    ],
                    'relation' => [
                        'prop' => 'foobar',
                        'subrelation' => [
                            'subprop' => 'barfoo',
                        ],
                    ],
                    'relation2' => [
                        'subrelation' => [
                            "(type='test')" => [
                                'type' => 'test',
                                'subprop' => 'barfoo',
                            ],
                        ],
                    ],
                    'baseModule' => User::class,
                ],
            ],
        ];
    }

    public function testModelWithRelatedEntities(): void
    {
        // Given
        $flow = new Flow;
        $flow->setBaseObject('something');
        $flow->setExternal(false);

        $listId = $this->tester->generateDynamicList();
        $linkCellId = $this->tester->generateListLinkCell($listId, [
            'cell_id' => $this->tester->generateListCell(),
        ]);
        $securityGroupId = $this->tester->generateSecurityGroup("blah");
        $filterId = $this->tester->generateFilter();

        $data = new FlowData($flow, new Model([
            'baseModule' => ListDynamic::class,
            'id' => $listId,
            'baseObject' => 'bar',
            'cellLinks|id' => $linkCellId,
            'cellLinks|order' => 100,
            'cellLinks|cell|subprop' => 'barfoo',
            "cellLinks|cell(type='test')|subprop" => 'barfoo',
            'securityGroups|id' => $securityGroupId,
            'filter|id' => $filterId,
            'filter|key' => 'my filter',
        ]));

        // When
        $this->modelConverter->handle($data);

        // Then
        $this->tester->assertEquals(
            [
                ListDynamic::class => [
                    'id' => $listId,
                    'baseObject' => 'bar',
                ],
                'cellLinks' => [
                    'id' => $linkCellId,
                    'order' => 100,
                    'cell' => [
                        'subprop' => 'barfoo',
                        "(type='test')" => [
                            'type' => 'test',
                            'subprop' => 'barfoo',
                        ],
                    ],
                ],
                'filter' => [
                    'id' => $filterId,
                    'key' => 'my filter',
                ],
                'securityGroups' => [
                    'id' => $securityGroupId,
                ],
                'baseModule' => ListDynamic::class,
            ],
            $data->getConvertedModel()
        );
    }
}
