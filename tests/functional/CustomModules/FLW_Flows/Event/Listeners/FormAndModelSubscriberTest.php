<?php declare(strict_types=1);

namespace Test\Functional\CustomModules\FLW_Flows\Event\Listeners;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\User;
use ExEss\Cms\FLW_Flows\Event\Listeners\FormAndModelSubscriber;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response\Model;
use Helper\Testcase\FunctionalTestCase;

class FormAndModelSubscriberTest extends FunctionalTestCase
{
    private FormAndModelSubscriber $subscriber;

    public function _before(): void
    {
        $this->subscriber = $this->tester->grabService(FormAndModelSubscriber::class);
    }

    public function testDefaultDateTimeIsConvertedToLocalTime(): void
    {
        // Given
        $field = new \stdClass();
        $field->id = 'pricing_date';
        $field->type = 'datetime';
        $field->default = 'NOW';

        $model = new Model;

        // When
        $this->subscriber->fillModelFromFields(
            [$field],
            $model,
            $model
        );

        // Then
        $this->tester->assertAlmostNow(new \DateTime($model->pricing_date));
    }

    public function testUpdateModelValuesBaseOnFieldType(): void
    {
        // Given
        $fieldId = $this->tester->generateGuidanceField([
            "name" => 'previous supplier',
            "field_id" => 'previous_supplier_c',
            "field_label" => 'Previous Supplier',
            "field_type" => FlowFieldType::FIELD_TYPE_SELECT_WITH_SEARCH,
            "field_generatebyserver" => 1,
            "field_module" => User::class,
            "field_modulefield" => 'previous_supplier_c',
            "field_multiple" => false,
            "field_custom" => \json_encode([
                "plusButtonTitle" => "Select Supplier",
                "modalTitle" => "Select Supplier",
                "selectedResultsTitle" => "Selected Supplier",
                "datasourceName" => "market_supplier_list",
            ]),
        ]);

        $flowId = $this->tester->generateGuidanceWitRepeatableBlock($flowKey = "flow-key", $repeatableKey = "repeat");

        $flowStepId = $this->tester->grabFromDatabase(
            'flw_flowstepslink',
            'flow_step_id',
            ["flow_id" => $flowId]
        );
        $this->tester->linkGuidanceFieldToFlowStep($fieldId, $flowStepId);

        $childFlowId = $this->tester->generateFlow(["key_c" => "my-child-flow-key"]);
        $childFlowStepId = $this->tester->generateFlowSteps($childFlowId, [], ["name" => "child-grid"]);
        $this->tester->linkGuidanceFieldToFlowStep($fieldId, $childFlowStepId);

        $model = new Model([
            "some-field" => [
                [
                    "key" => "key1",
                    "value" => "value1",
                ]
            ],
            "previous_supplier_c" => [
                [
                    "key" => "key2",
                    "value" => "value2",
                ]
            ],
            $repeatableKey => [
                "first-child-key" => [
                    "some-field" => [
                        [
                            "key" => "key3",
                            "value" => "value3",
                        ]
                    ],
                    "previous_supplier_c" => [
                        [
                            "key" => "key4",
                            "value" => "value4",
                        ]
                    ],
                ],
                "second-child-key" => [
                    "some-field" => [
                        [
                            "key" => "key5",
                            "value" => "value5",
                        ]
                    ],
                    "previous_supplier_c" => [
                        [
                            "key" => "key6",
                            "value" => "value6",
                        ]
                    ],
                ]
            ]
        ]);

        $event = new FlowEvent(
            $flowKey,
            new FlowAction(['event' => FlowAction::EVENT_CONFIRM]),
            $model
        );

        $event->getResponse()->setModel($model);
        $event->setFlow($this->tester->grabEntityFromRepository(Flow::class, ['id' => $flowId]));

        // When
        $this->subscriber->updateModelValuesBaseOnFieldType($event);

        // Then
        $this->tester->assertEquals(
            [
                "some-field" => [
                    [
                        "key" => "key1",
                        "value" => "value1",
                    ]
                ],
                "previous_supplier_c" => "key2",
                $repeatableKey => [
                    "first-child-key" => [
                        "some-field" => [
                            [
                                "key" => "key3",
                                "value" => "value3",
                            ]
                        ],
                        "previous_supplier_c" => "key4",
                    ],
                    "second-child-key" => [
                        "some-field" => [
                            [
                                "key" => "key5",
                                "value" => "value5",
                            ]
                        ],
                        "previous_supplier_c" => "key6",
                    ]
                ]
            ],
            $event->getModel()->toArray()
        );
    }
}
