<?php declare(strict_types=1);

namespace Test\Functional\CustomModules\FLW_Flows\Subscribers;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Entity\ListCellLink;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\FLW_Flows\Event\Listeners\ValueExpressionsSubscriber;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Model;
use Helper\Testcase\FunctionalTestCase;

class ValueExpressionsTest extends FunctionalTestCase
{
    protected ValueExpressionsSubscriber $valueExpression;

    public function _before(): void
    {
        $this->valueExpression = $this->tester->grabService(ValueExpressionsSubscriber::class);

        $this->tester->loadJsonFixturesFrom(__DIR__ . '/fixtures/content.json');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testResolveExpressionsShouldReplace(string $valueExpression, Model $model, string $expected): void
    {
        // setup
        $event = $this->generalSetUp($valueExpression, $model);

        // run test
         $this->valueExpression->resolveExpressions($event);

        // asserts
        $this->tester->assertEquals($expected, $event->getModel()->field_id);
    }

    public function testResolveExpressionsFakeModelKey(): void
    {
        // setup
        $valueExpression = '{iterator}dwp|fake_name;<p>UOM %uom_translated%</p>{/iterator}';
        $event = $this->generalSetUp($valueExpression, new Model());

        // assert
        $this->tester->expectThrowable(
            \InvalidArgumentException::class,
            function () use ($event): void {
                $this->valueExpression->resolveExpressions($event);
            }
        );
    }

    public function testMultipleResults(): void
    {
        // Given
        $listId = $this->tester->generateDynamicList();
        $this->tester->generateListLinkCell($listId, ['order_c' => 10]);
        $this->tester->generateListLinkCell($listId, ['order_c' => 20]);

        $valueExpression = '%cellLinks{order: order desc}[]|order%';
        $model = new Model();

        $event = $this->generalSetUp($valueExpression, $model);
        $event->setBaseEntity(
            $this->tester->grabEntityFromRepository(ListDynamic::class, ['id' => $listId]),
        );

        // When
        $this->valueExpression->resolveExpressions($event);

        // Then
        $this->tester->assertEquals('20, 10', $model->getFieldValue('field_id'));
    }

    public function testSelectWithSearch(): void
    {
        // Given
        $listId = $this->tester->generateDynamicList();
        $linkCellId1 = $this->tester->generateListLinkCell($listId, ['order_c' => 10]);
        $linkCellId2 = $this->tester->generateListLinkCell($listId, ['order_c' => 20]);

        $value1 = 'value 1';
        $value2 = 'value 2';

        $event = $this->generalSetUp(
            "{if}'%my_field%' == '$value1';$linkCellId1;{if}'%my_field%' == '$value2';$linkCellId2{/if}{/if}",
            $model = new Model(['my_field' => $value1])
        );

        $swsName = $this->tester->generateUuid();
        $this->tester->generateSelectWithSearchDatasource([
            'name' => $swsName,
            'base_object' => ListCellLink::class,
            'option_label' => "%id% - %order%",
        ]);

        $field = $event->getResponse()->getForm()->getGroup('abc')->fields[0];
        $field->type = FlowFieldType::FIELD_TYPE_SELECT_WITH_SEARCH;
        $field->datasourceName = $swsName;

        // When
        $this->valueExpression->resolveExpressions($event);

        // Then
        $this->tester->assertEquals(
            "[{\"key\":\"$linkCellId1\",\"label\":\"$linkCellId1 - 10\"}]",
            \json_encode($model->getFieldValue('field_id'))
        );

        // Given
        $model->my_field = $value2;

        // When
        $this->valueExpression->resolveExpressions($event);

        //Then
        $this->tester->assertEquals(
            "[{\"key\":\"$linkCellId2\",\"label\":\"$linkCellId2 - 20\"}]",
            \json_encode($model->getFieldValue('field_id'))
        );
    }

    private function generalSetUp(string $valueExpression, Model $model): FlowEvent
    {
        // setup
        $action = new FlowAction([
            'event' => FlowAction::EVENT_NEXT_STEP,
            'currentStep' => 'B2C_CQ_FLOW_STEP'
        ]);

        $event = new FlowEvent('B2C_FLOW_TEST', $action, $model);
        $form = new Response\Form('form-id', 'form-type', 'form-key', 'form-name');

        $field = new \stdClass();
        $field->id = 'field_id';
        $field->valueExpression = $valueExpression;

        $form->setGroup('abc', [$field]);

        $event->getResponse()->setForm($form);

        return $event;
    }

    public function dataProvider(): array
    {
        // @codingStandardsIgnoreStart
        return [
            [
                '{iterator}dwp|discount_assigned;{/iterator}',
                new Model([
                    'dwp|discount_assigned' => [
                        '0' => '4f700998-6a15-2616-581b-591f0042b52d'
                    ]
                ]),
                ''
            ],
            [
                '{iterator}dwp|discount_assigned;{/iterator}',
                new Model([
                    'dwp|discount_assigned' => [
                        '0' => '4f700998-6a15-2616-581b-591f0042b52d'
                    ]
                ]),
                ''
            ],
            [
                '{iterator}dwp|discount_assigned;{/iterator}',
                new Model([
                    'dwp|discount_assigned' => '4f700998-6a15-2616-581b-591f0042b52d'
                ]),
                ''
            ],
            [
                '{iterator}dwp|discount_assigned;<p>DISCOUNT DESC: {translate}%id%;discount-description{/translate}</p><p>DISCOUNT GEN CON: {translate}%id%;discount-general-conditions{/translate}</p>{/iterator}',
                new Model([
                    'dwp|discount_assigned' => [
                        '4f700998-6a15-2616-581b-591f0042b52d',
                        'b3eff5ef-2d32-e389-9750-5922bd76b21f'
                    ]
                ]),
                '<p>DISCOUNT DESC: 4f700998-6a15-2616-581b-591f0042b52d</p><p>DISCOUNT GEN CON: 4f700998-6a15-2616-581b-591f0042b52d</p><p>DISCOUNT DESC: uw welkoms korting.</p><p>DISCOUNT GEN CON: test general conditions</p>'
            ],
            [
                '%dwp|bla%',
                new Model([
                    'dwp|bla' => "0",
                    'field_id' => null,
                ]),
                "0"
            ],
            [
                '{iterator}dwp|discount_assigned;<p>DISCOUNT DESC: {translate!}%id%;discount-description{/translate!}</p><p>DISCOUNT GEN CON: {translate!}%id%;discount-general-conditions{/translate!}</p>{/iterator}',
                new Model([
                    'dwp|discount_assigned' => [
                        '4f700998-6a15-2616-581b-591f0042b52d',
                        'b3eff5ef-2d32-e389-9750-5922bd76b21f'
                    ]
                ]),
                '<p>DISCOUNT DESC: </p><p>DISCOUNT GEN CON: </p><p>DISCOUNT DESC: uw welkoms korting.</p><p>DISCOUNT GEN CON: test general conditions</p>'
            ],

        ];
        // @codingStandardsIgnoreEnd
    }
}
